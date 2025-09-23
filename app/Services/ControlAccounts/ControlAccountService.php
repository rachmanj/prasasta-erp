<?php

namespace App\Services\ControlAccounts;

use App\Models\ControlAccount;
use App\Models\SubsidiaryLedgerAccount;
use App\Models\ControlAccountBalance;
use App\Models\Accounting\JournalLine;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ControlAccountService
{
    /**
     * Create a new control account
     */
    public function createControlAccount(array $data): ControlAccount
    {
        return DB::transaction(function () use ($data) {
            $controlAccount = ControlAccount::create($data);

            // Create initial balance record for current period
            $this->createInitialBalance($controlAccount);

            return $controlAccount;
        });
    }

    /**
     * Create a subsidiary account
     */
    public function createSubsidiaryAccount(int $controlAccountId, array $data): SubsidiaryLedgerAccount
    {
        return DB::transaction(function () use ($controlAccountId, $data) {
            $data['control_account_id'] = $controlAccountId;
            $subsidiaryAccount = SubsidiaryLedgerAccount::create($data);

            // Update control account balance
            $this->updateControlAccountBalance($controlAccountId);

            return $subsidiaryAccount;
        });
    }

    /**
     * Update balances when journal is posted
     */
    public function updateBalances(int $journalId): void
    {
        DB::transaction(function () use ($journalId) {
            $journalLines = JournalLine::where('journal_id', $journalId)->get();

            foreach ($journalLines as $line) {
                $this->updateAccountBalance($line);
            }
        });
    }

    /**
     * Reconcile a control account for a specific period
     */
    public function reconcileAccount(int $controlAccountId, string $period): ControlAccountBalance
    {
        return DB::transaction(function () use ($controlAccountId, $period) {
            $controlAccount = ControlAccount::findOrFail($controlAccountId);
            $balance = $this->getOrCreateBalance($controlAccountId, $period);

            // Calculate control balance from journal lines
            $controlBalance = $this->calculateControlBalance($controlAccountId, $period);

            // Calculate subsidiary total
            $subsidiaryTotal = $this->calculateSubsidiaryTotal($controlAccountId, $period);

            // Calculate variance
            $variance = $controlBalance - $subsidiaryTotal;

            // Update balance record
            $balance->update([
                'calculated_balance' => $controlBalance,
                'subsidiary_total' => $subsidiaryTotal,
                'variance_amount' => $variance,
                'reconciliation_status' => abs($variance) <= $controlAccount->tolerance_amount ? 'reconciled' : 'variance',
            ]);

            return $balance;
        });
    }

    /**
     * Generate reconciliation report for a period
     */
    public function generateReconciliationReport(string $period): array
    {
        $controlAccounts = ControlAccount::active()->get();
        $report = [];

        foreach ($controlAccounts as $account) {
            $balance = $this->getOrCreateBalance($account->id, $period);
            $this->reconcileAccount($account->id, $period);

            $report[] = [
                'control_account' => $account,
                'balance' => $balance->fresh(),
                'variance_percentage' => $balance->getVariancePercentage(),
                'status' => $balance->reconciliation_status,
            ];
        }

        return $report;
    }

    /**
     * Calculate control balance from journal lines
     */
    public function calculateControlBalance(int $controlAccountId, string $period): float
    {
        $startDate = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $period)->endOfMonth();

        // Get opening balance
        $openingBalance = $this->getOpeningBalance($controlAccountId, $period);

        // Calculate net movement from journal lines
        $netMovement = JournalLine::whereHas('journal', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })
            ->where('account_id', $controlAccountId)
            ->selectRaw('SUM(debit - credit) as net_movement')
            ->value('net_movement') ?? 0;

        return $openingBalance + $netMovement;
    }

    /**
     * Calculate subsidiary total
     */
    public function calculateSubsidiaryTotal(int $controlAccountId, string $period): float
    {
        return SubsidiaryLedgerAccount::where('control_account_id', $controlAccountId)
            ->where('is_active', true)
            ->sum('current_balance');
    }

    /**
     * Identify accounts with variances
     */
    public function identifyVariances(string $period): array
    {
        $variances = [];
        $controlAccounts = ControlAccount::active()->get();

        foreach ($controlAccounts as $account) {
            $balance = $this->getOrCreateBalance($account->id, $period);
            $this->reconcileAccount($account->id, $period);

            if ($balance->fresh()->hasVariance()) {
                $variances[] = [
                    'control_account' => $account,
                    'balance' => $balance->fresh(),
                    'variance_amount' => $balance->variance_amount,
                    'variance_percentage' => $balance->getVariancePercentage(),
                ];
            }
        }

        return $variances;
    }

    /**
     * Get or create balance record for a period
     */
    public function getOrCreateBalance(int $controlAccountId, string $period): ControlAccountBalance
    {
        return ControlAccountBalance::firstOrCreate(
            ['control_account_id' => $controlAccountId, 'period' => $period],
            [
                'opening_balance' => $this->getOpeningBalance($controlAccountId, $period),
                'total_debits' => 0,
                'total_credits' => 0,
                'calculated_balance' => 0,
                'subsidiary_total' => 0,
                'variance_amount' => 0,
                'reconciliation_status' => 'pending',
            ]
        );
    }

    /**
     * Get opening balance for a period
     */
    private function getOpeningBalance(int $controlAccountId, string $period): float
    {
        $previousPeriod = Carbon::createFromFormat('Y-m', $period)->subMonth()->format('Y-m');

        $previousBalance = ControlAccountBalance::where('control_account_id', $controlAccountId)
            ->where('period', $previousPeriod)
            ->first();

        return $previousBalance ? $previousBalance->calculated_balance : 0.00;
    }

    /**
     * Create initial balance record
     */
    private function createInitialBalance(ControlAccount $controlAccount): void
    {
        $currentPeriod = now()->format('Y-m');

        ControlAccountBalance::create([
            'control_account_id' => $controlAccount->id,
            'period' => $currentPeriod,
            'opening_balance' => 0.00,
            'total_debits' => 0.00,
            'total_credits' => 0.00,
            'calculated_balance' => 0.00,
            'subsidiary_total' => 0.00,
            'variance_amount' => 0.00,
            'reconciliation_status' => 'pending',
        ]);
    }

    /**
     * Update control account balance
     */
    private function updateControlAccountBalance(int $controlAccountId): void
    {
        $currentPeriod = now()->format('Y-m');
        $balance = $this->getOrCreateBalance($controlAccountId, $currentPeriod);

        $subsidiaryTotal = $this->calculateSubsidiaryTotal($controlAccountId, $currentPeriod);
        $controlBalance = $this->calculateControlBalance($controlAccountId, $currentPeriod);

        $balance->update([
            'subsidiary_total' => $subsidiaryTotal,
            'calculated_balance' => $controlBalance,
            'variance_amount' => $controlBalance - $subsidiaryTotal,
        ]);
    }

    /**
     * Update individual account balance
     */
    private function updateAccountBalance(JournalLine $line): void
    {
        // Find subsidiary account if it exists
        $subsidiaryAccount = SubsidiaryLedgerAccount::where('account_id', $line->account_id)->first();

        if ($subsidiaryAccount) {
            $netAmount = $line->debit - $line->credit;
            $subsidiaryAccount->addToBalance($netAmount);

            // Update control account balance
            $this->updateControlAccountBalance($subsidiaryAccount->control_account_id);
        }
    }

    /**
     * Validate control account posting
     */
    public function validateControlAccountPosting(int $accountId, float $amount): bool
    {
        $controlAccount = ControlAccount::find($accountId);

        if (!$controlAccount || !$controlAccount->is_active) {
            return false;
        }

        // Additional validation logic can be added here
        return true;
    }

    /**
     * Get control account statistics
     */
    public function getControlAccountStatistics(): array
    {
        $totalAccounts = ControlAccount::active()->count();
        $totalSubsidiaries = SubsidiaryLedgerAccount::active()->count();
        $pendingReconciliations = ControlAccountBalance::pending()->count();
        $accountsWithVariance = ControlAccountBalance::withVariance()->count();

        return [
            'total_control_accounts' => $totalAccounts,
            'total_subsidiary_accounts' => $totalSubsidiaries,
            'pending_reconciliations' => $pendingReconciliations,
            'accounts_with_variance' => $accountsWithVariance,
        ];
    }
}

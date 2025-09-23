<?php

namespace App\Http\Controllers\Banking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BankingDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('banking.dashboard');
    }

    public function data(Request $request)
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Daily Cash Flow
        $dailyCashOut = DB::table('cash_outs')
            ->whereDate('date', $today)
            ->sum('total_amount');

        $dailyCashIn = DB::table('cash_ins')
            ->whereDate('date', $today)
            ->sum('total_amount');

        $dailyNetFlow = $dailyCashIn - $dailyCashOut;

        // Monthly Cash Flow
        $monthlyCashOut = DB::table('cash_outs')
            ->where('date', '>=', $thisMonth)
            ->sum('total_amount');

        $monthlyCashIn = DB::table('cash_ins')
            ->where('date', '>=', $thisMonth)
            ->sum('total_amount');

        $monthlyNetFlow = $monthlyCashIn - $monthlyCashOut;

        // Previous Month Comparison
        $prevMonthCashOut = DB::table('cash_outs')
            ->whereBetween('date', [$lastMonth, $thisMonth->copy()->subDay()])
            ->sum('total_amount');

        $prevMonthCashIn = DB::table('cash_ins')
            ->whereBetween('date', [$lastMonth, $thisMonth->copy()->subDay()])
            ->sum('total_amount');

        $prevMonthNetFlow = $prevMonthCashIn - $prevMonthCashOut;

        // Cash/Bank Account Balances
        $cashAccounts = DB::table('accounts')
            ->where('code', 'like', '1.1.2%')
            ->where('is_postable', 1)
            ->get(['id', 'code', 'name']);

        $accountBalances = [];
        foreach ($cashAccounts as $account) {
            $debitTotal = DB::table('journal_lines')
                ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
                ->where('journal_lines.account_id', $account->id)
                ->where('journals.status', 'posted')
                ->sum('journal_lines.debit');

            $creditTotal = DB::table('journal_lines')
                ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
                ->where('journal_lines.account_id', $account->id)
                ->where('journals.status', 'posted')
                ->sum('journal_lines.credit');

            $balance = $debitTotal - $creditTotal;
            $accountBalances[] = [
                'account' => $account,
                'balance' => $balance
            ];
        }

        // Recent Transactions (last 10)
        $recentTransactions = collect();

        // Get recent cash outs
        $recentCashOuts = DB::table('cash_outs')
            ->join('accounts', 'accounts.id', '=', 'cash_outs.cash_account_id')
            ->join('users', 'users.id', '=', 'cash_outs.created_by')
            ->select('cash_outs.id', 'cash_outs.voucher_number', 'cash_outs.date', 'cash_outs.description', 'cash_outs.total_amount', 'accounts.name as account_name', 'users.name as creator_name')
            ->addSelect(DB::raw("'cash_out' as type"))
            ->orderBy('cash_outs.created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent cash ins
        $recentCashIns = DB::table('cash_ins')
            ->join('accounts', 'accounts.id', '=', 'cash_ins.cash_account_id')
            ->join('users', 'users.id', '=', 'cash_ins.created_by')
            ->select('cash_ins.id', 'cash_ins.voucher_number', 'cash_ins.date', 'cash_ins.description', 'cash_ins.total_amount', 'accounts.name as account_name', 'users.name as creator_name')
            ->addSelect(DB::raw("'cash_in' as type"))
            ->orderBy('cash_ins.created_at', 'desc')
            ->limit(5)
            ->get();

        $recentTransactions = $recentCashOuts->concat($recentCashIns)
            ->sortByDesc('created_at')
            ->take(10);

        // Top Expense Categories (this month)
        $topExpenses = DB::table('cash_out_lines')
            ->join('cash_outs', 'cash_outs.id', '=', 'cash_out_lines.cash_out_id')
            ->join('accounts', 'accounts.id', '=', 'cash_out_lines.account_id')
            ->where('cash_outs.date', '>=', $thisMonth)
            ->select('accounts.name as account_name', DB::raw('SUM(cash_out_lines.amount) as total_amount'))
            ->groupBy('accounts.id', 'accounts.name')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        // Top Revenue Categories (this month)
        $topRevenues = DB::table('cash_in_lines')
            ->join('cash_ins', 'cash_ins.id', '=', 'cash_in_lines.cash_in_id')
            ->join('accounts', 'accounts.id', '=', 'cash_in_lines.account_id')
            ->where('cash_ins.date', '>=', $thisMonth)
            ->select('accounts.name as account_name', DB::raw('SUM(cash_in_lines.amount) as total_amount'))
            ->groupBy('accounts.id', 'accounts.name')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'daily' => [
                'cash_in' => $dailyCashIn,
                'cash_out' => $dailyCashOut,
                'net_flow' => $dailyNetFlow
            ],
            'monthly' => [
                'cash_in' => $monthlyCashIn,
                'cash_out' => $monthlyCashOut,
                'net_flow' => $monthlyNetFlow,
                'prev_month_net_flow' => $prevMonthNetFlow
            ],
            'account_balances' => $accountBalances,
            'recent_transactions' => $recentTransactions,
            'top_expenses' => $topExpenses,
            'top_revenues' => $topRevenues
        ]);
    }
}

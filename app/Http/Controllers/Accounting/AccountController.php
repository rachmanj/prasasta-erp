<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('permission:accounts.view')->only(['index']);
        $this->middleware('permission:accounts.manage')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $query = Account::query();

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('code', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by account type
        if ($request->filled('type')) {
            $query->where('type', $request->get('type'));
        }

        // Filter by control type
        if ($request->filled('control_type')) {
            $query->where('control_type', $request->get('control_type'));
        }

        // Filter by control account status
        if ($request->filled('is_control_account')) {
            $query->where('is_control_account', $request->get('is_control_account'));
        }

        // Order by code for better organization
        $query->orderBy('code');

        // Paginate results
        $accounts = $query->paginate(20)->withQueryString();

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $parents = Account::query()->orderBy('code')->get();
        return view('accounts.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,net_assets,income,expense'],
            'is_postable' => ['required', 'boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'is_control_account' => ['nullable', 'boolean'],
            'control_type' => ['nullable', 'in:ap,ar,cash,inventory,fixed_assets,other'],
            'description' => ['nullable', 'string'],
            'reconciliation_frequency' => ['nullable', 'in:daily,weekly,monthly,quarterly,yearly'],
            'tolerance_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        // If marking as control account, ensure control_type is provided
        if (!empty($data['is_control_account']) && empty($data['control_type'])) {
            return redirect()->back()->withErrors(['control_type' => 'Control type is required when marking account as control account.'])->withInput();
        }

        // Set default values for control account fields
        if (!empty($data['is_control_account'])) {
            $data['reconciliation_frequency'] = $data['reconciliation_frequency'] ?? 'monthly';
            $data['tolerance_amount'] = $data['tolerance_amount'] ?? 0.00;
        }

        $account = Account::create($data);

        // Create corresponding ControlAccount record if marked as control account
        if (!empty($data['is_control_account'])) {
            try {
                $account->createControlAccount();
            } catch (\Exception $e) {
                \Log::error('Failed to create ControlAccount: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Account created but failed to create control account record.'])->withInput();
            }
        }

        return redirect()->route('accounts.index')->with('success', 'Account created successfully');
    }

    public function edit(Account $account)
    {
        $parents = Account::where('id', '!=', $account->id)->orderBy('code')->get();
        return view('accounts.edit', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:accounts,code,' . $account->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,net_assets,income,expense'],
            'is_postable' => ['required', 'boolean'],
            'parent_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'is_control_account' => ['nullable', 'boolean'],
            'control_type' => ['nullable', 'in:ap,ar,cash,inventory,fixed_assets,other'],
            'description' => ['nullable', 'string'],
            'reconciliation_frequency' => ['nullable', 'in:daily,weekly,monthly,quarterly,yearly'],
            'tolerance_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Check if children have journal transactions
        if ($account->hasChildrenWithTransactions()) {
            $errors = [];

            // Define protected fields that cannot be changed when children have transactions
            $protectedFields = [
                'type' => 'Account Type',
                'parent_id' => 'Parent Account',
                'is_postable' => 'Postable Status',
                'control_type' => 'Control Type',
                'is_control_account' => 'Control Account Status'
            ];

            // Check each protected field for changes
            foreach ($protectedFields as $field => $fieldLabel) {
                if (isset($data[$field]) && $data[$field] != $account->$field) {
                    $errors[$field] = "Cannot change {$fieldLabel} because child accounts have journal transactions. Please reverse or delete the transactions first.";
                }
            }

            // If there are validation errors, return them
            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }
        }

        // If marking as control account, ensure control_type is provided
        if (!empty($data['is_control_account']) && empty($data['control_type'])) {
            return redirect()->back()->withErrors(['control_type' => 'Control type is required when marking account as control account.'])->withInput();
        }

        // Set default values for control account fields
        if (!empty($data['is_control_account'])) {
            $data['reconciliation_frequency'] = $data['reconciliation_frequency'] ?? 'monthly';
            $data['tolerance_amount'] = $data['tolerance_amount'] ?? 0.00;
        }

        $account->update($data);

        // Handle ControlAccount record creation/update
        if (!empty($data['is_control_account'])) {
            try {
                // Check if ControlAccount already exists
                $controlAccount = $account->controlAccount;
                if ($controlAccount) {
                    // Update existing ControlAccount
                    $controlAccount->update([
                        'name' => $account->name,
                        'type' => $account->type,
                        'control_type' => $account->control_type,
                        'reconciliation_frequency' => $account->reconciliation_frequency,
                        'tolerance_amount' => $account->tolerance_amount,
                        'description' => $account->description,
                    ]);
                } else {
                    // Create new ControlAccount
                    $account->createControlAccount();
                }
            } catch (\Exception $e) {
                \Log::error('Failed to update ControlAccount: ' . $e->getMessage());
                return redirect()->back()->withErrors(['error' => 'Account updated but failed to update control account record.'])->withInput();
            }
        } else {
            // If unmarking as control account, deactivate the ControlAccount
            if ($account->controlAccount) {
                $account->controlAccount->update(['is_active' => false]);
            }
        }

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully');
    }

    public function show(Account $account)
    {
        return view('accounts.show', compact('account'));
    }

    public function transactionsData(Request $request, Account $account)
    {
        try {
            $query = $account->journalLines()
                ->join('journals', 'journal_lines.journal_id', '=', 'journals.id')
                ->leftJoin('users', 'journals.posted_by', '=', 'users.id')
                ->select([
                    'journal_lines.*',
                    'journals.date as posting_date',
                    'journals.journal_no as journal_number',
                    'journals.source_type as origin_document',
                    'journals.description',
                    'journals.created_at',
                    'users.name as created_by_name'
                ]);

            // Apply date filters
            if ($request->filled('date_from')) {
                $query->where('journals.date', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('journals.date', '<=', $request->get('date_to'));
            }

            // Order by posting date ascending (oldest first)
            $query->orderBy('journals.date', 'asc')
                ->orderBy('journals.created_at', 'asc');

            $transactions = $query->get();

            // Calculate running balance
            $runningBalance = 0;
            $data = $transactions->map(function ($transaction) use (&$runningBalance) {
                $runningBalance += $transaction->debit - $transaction->credit;

                return [
                    'posting_date' => $transaction->posting_date ? \Carbon\Carbon::parse($transaction->posting_date)->format('d/m/Y') : '-',
                    'created_at' => \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y'),
                    'journal_number' => $transaction->journal_number ?? '-',
                    'origin_document' => $transaction->origin_document ?? '-',
                    'description' => $transaction->description ?? '-',
                    'debit' => $transaction->debit > 0 ? 'Rp ' . number_format($transaction->debit, 2, ',', '.') : '-',
                    'credit' => $transaction->credit > 0 ? 'Rp ' . number_format($transaction->credit, 2, ',', '.') : '-',
                    'running_balance' => 'Rp ' . number_format($runningBalance, 2, ',', '.'),
                    'created_by' => $transaction->created_by_name ?? '-'
                ];
            });

            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => $transactions->count(),
                'recordsFiltered' => $transactions->count(),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load account transactions: ' . $e->getMessage());
            return response()->json([
                'draw' => intval($request->get('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to load transactions'
            ]);
        }
    }

    public function transactionsExport(Request $request, Account $account)
    {
        try {
            $query = $account->journalLines()
                ->join('journals', 'journal_lines.journal_id', '=', 'journals.id')
                ->leftJoin('users', 'journals.posted_by', '=', 'users.id')
                ->select([
                    'journal_lines.*',
                    'journals.date as posting_date',
                    'journals.journal_no as journal_number',
                    'journals.source_type as origin_document',
                    'journals.description',
                    'journals.created_at',
                    'users.name as created_by_name'
                ]);

            // Apply date filters (posting date only)
            if ($request->filled('date_from')) {
                $query->where('journals.date', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('journals.date', '<=', $request->get('date_to'));
            }

            // Order by posting date ascending (oldest first)
            $query->orderBy('journals.date', 'asc')
                ->orderBy('journals.created_at', 'asc');

            $transactions = $query->get();

            // Calculate running balance
            $runningBalance = 0;
            $data = $transactions->map(function ($transaction) use (&$runningBalance) {
                $runningBalance += $transaction->debit - $transaction->credit;

                return [
                    'posting_date' => $transaction->posting_date ? \Carbon\Carbon::parse($transaction->posting_date)->format('d/m/Y') : '-',
                    'created_at' => \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y'),
                    'journal_number' => $transaction->journal_number ?? '-',
                    'origin_document' => $transaction->origin_document ?? '-',
                    'description' => $transaction->description ?? '-',
                    'debit' => $transaction->debit > 0 ? $transaction->debit : 0,
                    'credit' => $transaction->credit > 0 ? $transaction->credit : 0,
                    'running_balance' => $runningBalance,
                    'created_by' => $transaction->created_by_name ?? '-'
                ];
            });

            // Generate Excel file
            $filename = 'Account_' . $account->code . '_Transactions_' . date('Y-m-d') . '.xlsx';

            return response()->streamDownload(function () use ($data, $account) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fwrite($file, "\xEF\xBB\xBF");

                // Write headers
                fputcsv($file, [
                    'Posting Date',
                    'Create Date',
                    'Journal Number',
                    'Origin Document',
                    'Description',
                    'Debit',
                    'Credit',
                    'Running Balance',
                    'Created By'
                ]);

                // Write data
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row['posting_date'],
                        $row['created_at'],
                        $row['journal_number'],
                        $row['origin_document'],
                        $row['description'],
                        $row['debit'],
                        $row['credit'],
                        $row['running_balance'],
                        $row['created_by']
                    ]);
                }

                fclose($file);
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to export account transactions: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to export transactions. Please try again.']);
        }
    }
}

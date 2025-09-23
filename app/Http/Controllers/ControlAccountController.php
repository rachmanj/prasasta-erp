<?php

namespace App\Http\Controllers;

use App\Models\ControlAccount;
use App\Models\SubsidiaryLedgerAccount;
use App\Models\ControlAccountBalance;
use App\Models\Accounting\Account;
use App\Services\ControlAccounts\ControlAccountService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ControlAccountController extends Controller
{
    public function __construct(
        private ControlAccountService $controlAccountService
    ) {
        $this->middleware(['auth', 'permission:control_accounts.view']);
    }

    /**
     * Display a listing of control accounts
     */
    public function index()
    {
        return view('control-accounts.index');
    }

    /**
     * Get control accounts data for DataTables
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $query = ControlAccount::query();

            return DataTables::of($query)
                ->addColumn('current_balance', function ($account) {
                    return '0.00';
                })
                ->addColumn('subsidiary_total', function ($account) {
                    return '0.00';
                })
                ->addColumn('variance', function ($account) {
                    return '<span class="badge badge-success">0.00</span>';
                })
                ->addColumn('reconciliation_status', function ($account) {
                    return '<span class="badge badge-warning">Pending</span>';
                })
                ->addColumn('last_reconciliation', function ($account) {
                    return 'Never';
                })
                ->addColumn('actions', function ($account) {
                    $actions = '<div class="btn-group" role="group">';
                    $actions .= '<a href="' . route('control-accounts.show', $account->id) . '" class="btn btn-sm btn-info">View</a>';
                    $actions .= '<a href="' . route('control-accounts.edit', $account->id) . '" class="btn btn-sm btn-warning">Edit</a>';
                    $actions .= '<a href="' . route('control-accounts.reconciliation', $account->id) . '" class="btn btn-sm btn-primary">Reconcile</a>';
                    $actions .= '<a href="' . route('control-accounts.subsidiary-accounts', $account->id) . '" class="btn btn-sm btn-secondary">Subsidiaries</a>';

                    if (auth()->user()->can('control_accounts.delete')) {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-delete-url="' . route('control-accounts.destroy', $account->id) . '" data-name="' . $account->name . '">Delete</button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['variance', 'reconciliation_status', 'actions'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Control Account DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new control account
     */
    public function create()
    {
        $accounts = Account::where('is_postable', true)->get();
        return view('control-accounts.create', compact('accounts'));
    }

    /**
     * Store a newly created control account
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'code' => ['required', 'string', 'max:255', 'unique:control_accounts,code'],
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
                'control_type' => ['required', 'in:ar,ap,inventory,fixed_assets,cash,other'],
                'reconciliation_frequency' => ['required', 'in:daily,weekly,monthly'],
                'tolerance_amount' => ['required', 'numeric', 'min:0'],
                'description' => ['nullable', 'string'],
            ]);

            $controlAccount = $this->controlAccountService->createControlAccount($data);

            return redirect()->route('control-accounts.index')
                ->with('success', 'Control account created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the control account: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified control account
     */
    public function show(ControlAccount $controlAccount)
    {
        $controlAccount->load(['subsidiaryAccounts', 'balances']);

        // Get recent transactions
        $recentTransactions = DB::table('journal_lines as jl')
            ->join('journals as j', 'j.id', '=', 'jl.journal_id')
            ->where('jl.account_id', $controlAccount->id)
            ->orderBy('j.date', 'desc')
            ->limit(10)
            ->select('j.date', 'j.description', 'jl.debit', 'jl.credit', 'jl.memo')
            ->get();

        return view('control-accounts.show', compact('controlAccount', 'recentTransactions'));
    }

    /**
     * Show the form for editing the specified control account
     */
    public function edit(ControlAccount $controlAccount)
    {
        return view('control-accounts.edit', compact('controlAccount'));
    }

    /**
     * Update the specified control account
     */
    public function update(Request $request, ControlAccount $controlAccount)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:control_accounts,code,' . $controlAccount->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:asset,liability,equity,revenue,expense'],
            'control_type' => ['required', 'in:ar,ap,inventory,fixed_assets,cash,other'],
            'reconciliation_frequency' => ['required', 'in:daily,weekly,monthly'],
            'tolerance_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $controlAccount->update($data);

        return redirect()->route('control-accounts.show', $controlAccount)
            ->with('success', 'Control account updated successfully.');
    }

    /**
     * Remove the specified control account
     */
    public function destroy(ControlAccount $controlAccount)
    {
        try {
            // Check if control account has subsidiary accounts
            if ($controlAccount->subsidiaryAccounts()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete control account with subsidiary accounts.'
                    ], 400);
                }
                return redirect()->back()
                    ->with('error', 'Cannot delete control account with subsidiary accounts.');
            }

            // Check if control account has transactions
            $hasTransactions = DB::table('journal_lines')
                ->where('account_id', $controlAccount->id)
                ->exists();

            if ($hasTransactions) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete control account with transactions.'
                    ], 400);
                }
                return redirect()->back()
                    ->with('error', 'Cannot delete control account with transactions.');
            }

            $controlAccount->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Control account deleted successfully.'
                ]);
            }

            return redirect()->route('control-accounts.index')
                ->with('success', 'Control account deleted successfully.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the control account.'
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the control account.');
        }
    }

    /**
     * Show reconciliation interface
     */
    public function reconciliation(ControlAccount $controlAccount)
    {
        $currentPeriod = now()->format('Y-m');
        $balance = $this->controlAccountService->getOrCreateBalance($controlAccount->id, $currentPeriod);

        // Perform reconciliation
        $this->controlAccountService->reconcileAccount($controlAccount->id, $currentPeriod);
        $balance->refresh();

        return view('control-accounts.reconciliation', compact('controlAccount', 'balance'));
    }

    /**
     * Process reconciliation
     */
    public function reconcile(Request $request, ControlAccount $controlAccount)
    {
        $data = $request->validate([
            'period' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'action' => ['required', 'in:approve,reject'],
        ]);

        $balance = $this->controlAccountService->getOrCreateBalance($controlAccount->id, $data['period']);

        if ($data['action'] === 'approve') {
            $balance->markAsReconciled(Auth::id(), $data['notes']);
            $message = 'Reconciliation approved successfully.';
        } else {
            $balance->markAsVariance($data['notes']);
            $message = 'Reconciliation marked as variance.';
        }

        return redirect()->route('control-accounts.reconciliation', $controlAccount)
            ->with('success', $message);
    }

    /**
     * Manage subsidiary accounts
     */
    public function subsidiaryAccounts(ControlAccount $controlAccount)
    {
        $subsidiaryAccounts = $controlAccount->subsidiaryAccounts()
            ->orderBy('subsidiary_code')
            ->paginate(20);

        return view('control-accounts.subsidiary-accounts', compact('controlAccount', 'subsidiaryAccounts'));
    }

    /**
     * Get subsidiary accounts data for DataTables
     */
    public function subsidiaryAccountsData(ControlAccount $controlAccount): JsonResponse
    {
        try {
            $query = $controlAccount->subsidiaryAccounts();

            return DataTables::of($query)
                ->addColumn('actions', function ($account) {
                    $actions = '<div class="btn-group" role="group">';

                    if (auth()->user()->can('control_accounts.edit')) {
                        $actions .= '<button class="btn btn-sm btn-warning edit-btn" data-id="' . $account->id . '">Edit</button>';
                    }

                    if (auth()->user()->can('control_accounts.delete')) {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-delete-url="' . url('control-accounts/' . $controlAccount->id . '/subsidiary-accounts/' . $account->id) . '" data-name="' . $account->name . '">Delete</button>';
                    }

                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Subsidiary Accounts DataTables Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a new subsidiary account
     */
    public function storeSubsidiaryAccount(Request $request, ControlAccount $controlAccount)
    {
        $request->validate([
            'subsidiary_code' => 'required|string|max:50|unique:subsidiary_ledger_accounts,subsidiary_code',
            'name' => 'required|string|max:255',
            'subsidiary_type' => 'required|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'metadata' => 'nullable|string'
        ]);

        try {
            $metadata = null;
            if ($request->metadata) {
                $metadata = json_decode($request->metadata, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid JSON format in metadata field.'
                    ], 400);
                }
            }

            $subsidiaryAccount = $controlAccount->subsidiaryAccounts()->create([
                'subsidiary_code' => $request->subsidiary_code,
                'name' => $request->name,
                'subsidiary_type' => $request->subsidiary_type,
                'opening_balance' => $request->opening_balance ?? 0.00,
                'current_balance' => $request->opening_balance ?? 0.00,
                'metadata' => $metadata,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subsidiary account created successfully.',
                'data' => $subsidiaryAccount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating subsidiary account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the subsidiary account.'
            ], 500);
        }
    }

    /**
     * Show a specific subsidiary account
     */
    public function showSubsidiaryAccount(ControlAccount $controlAccount, SubsidiaryLedgerAccount $subsidiaryAccount)
    {
        return response()->json($subsidiaryAccount);
    }

    /**
     * Update a subsidiary account
     */
    public function updateSubsidiaryAccount(Request $request, ControlAccount $controlAccount, SubsidiaryLedgerAccount $subsidiaryAccount)
    {
        $request->validate([
            'subsidiary_code' => 'required|string|max:50|unique:subsidiary_ledger_accounts,subsidiary_code,' . $subsidiaryAccount->id,
            'name' => 'required|string|max:255',
            'subsidiary_type' => 'required|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'metadata' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            $metadata = null;
            if ($request->metadata) {
                $metadata = json_decode($request->metadata, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid JSON format in metadata field.'
                    ], 400);
                }
            }

            $subsidiaryAccount->update([
                'subsidiary_code' => $request->subsidiary_code,
                'name' => $request->name,
                'subsidiary_type' => $request->subsidiary_type,
                'opening_balance' => $request->opening_balance ?? $subsidiaryAccount->opening_balance,
                'metadata' => $metadata,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : $subsidiaryAccount->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subsidiary account updated successfully.',
                'data' => $subsidiaryAccount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating subsidiary account: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the subsidiary account.'
            ], 500);
        }
    }

    /**
     * Delete a subsidiary account
     */
    public function destroySubsidiaryAccount(ControlAccount $controlAccount, SubsidiaryLedgerAccount $subsidiaryAccount)
    {
        try {
            // Check if subsidiary account has transactions
            if ($subsidiaryAccount->hasTransactions()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete subsidiary account with existing transactions.'
                    ], 400);
                }
                return redirect()->back()
                    ->with('error', 'Cannot delete subsidiary account with existing transactions.');
            }

            $subsidiaryAccount->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subsidiary account deleted successfully.'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Subsidiary account deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting subsidiary account: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the subsidiary account.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'An error occurred while deleting the subsidiary account.');
        }
    }

    /**
     * Show balance history
     */
    public function balances(ControlAccount $controlAccount)
    {
        $balances = $controlAccount->balances()
            ->orderBy('period', 'desc')
            ->paginate(20);

        return view('control-accounts.balances', compact('controlAccount', 'balances'));
    }

    /**
     * Get balance history data for DataTables
     */
    public function balancesData(ControlAccount $controlAccount): JsonResponse
    {
        $query = $controlAccount->balances();

        return DataTables::of($query)
            ->addColumn('period_formatted', function ($balance) {
                return $balance->getPeriodFormatted();
            })
            ->addColumn('calculated_balance_formatted', function ($balance) {
                return number_format($balance->calculated_balance, 2);
            })
            ->addColumn('subsidiary_total_formatted', function ($balance) {
                return number_format($balance->subsidiary_total, 2);
            })
            ->addColumn('variance_formatted', function ($balance) {
                return number_format($balance->variance_amount, 2);
            })
            ->addColumn('reconciliation_status_badge', function ($balance) {
                return $balance->getReconciliationStatusBadge();
            })
            ->addColumn('reconciled_date_formatted', function ($balance) {
                return $balance->getReconciledDateFormatted();
            })
            ->addColumn('reconciled_by_formatted', function ($balance) {
                return $balance->getReconciledByFormatted();
            })
            ->rawColumns(['reconciliation_status_badge'])
            ->make(true);
    }

    /**
     * Get reconciliation dashboard data
     */
    public function dashboardData(): JsonResponse
    {
        $statistics = $this->controlAccountService->getControlAccountStatistics();
        $variances = $this->controlAccountService->identifyVariances(now()->format('Y-m'));

        return response()->json([
            'statistics' => $statistics,
            'variances' => $variances,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Banking;

use App\Http\Controllers\Controller;
use App\Models\Banking\CashOut;
use App\Services\Accounting\PostingService;
use App\Services\DocumentNumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CashOutController extends Controller
{
    public function __construct(
        private PostingService $posting,
        private DocumentNumberingService $numberingService
    ) {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return view('banking.cash_out.index');
    }

    public function create()
    {
        // Get all postable accounts (for debit lines)
        $debitAccounts = DB::table('accounts')
            ->where('is_postable', 1)
            ->whereIn('type', ['expense', 'asset', 'liability']) // Allow various debit accounts
            ->orderBy('code')
            ->get();

        // Get cash/bank accounts (for credit)
        $cashAccounts = DB::table('accounts')
            ->where('code', 'like', '1.1.2%')
            ->where('is_postable', 1)
            ->orderBy('code')
            ->get();

        $projects = DB::table('projects')->orderBy('code')->get(['id', 'code', 'name']);
        $funds = DB::table('funds')->orderBy('code')->get(['id', 'code', 'name']);
        $departments = DB::table('departments')->orderBy('code')->get(['id', 'code', 'name']);

        return view('banking.cash_out.create', compact('debitAccounts', 'cashAccounts', 'projects', 'funds', 'departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'cash_account_id' => ['required', 'integer', 'exists:accounts,id'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.account_id' => ['required', 'integer', 'exists:accounts,id'],
            'lines.*.amount' => ['required', 'numeric', 'min:0.01'],
            'lines.*.memo' => ['nullable', 'string', 'max:255'],
            'lines.*.project_id' => ['nullable', 'integer'],
            'lines.*.fund_id' => ['nullable', 'integer'],
            'lines.*.dept_id' => ['nullable', 'integer'],
            'project_id' => ['nullable', 'integer'],
            'fund_id' => ['nullable', 'integer'],
            'dept_id' => ['nullable', 'integer'],
        ]);

        return DB::transaction(function () use ($data) {
            // Calculate total amount
            $totalAmount = collect($data['lines'])->sum('amount');

            // Generate voucher number using new numbering system
            $voucherNumber = $this->numberingService->generateNumber('cash_outs', $data['date']);

            // Create cash out record
            $cashOut = CashOut::create([
                'voucher_number' => $voucherNumber,
                'date' => $data['date'],
                'description' => $data['description'],
                'cash_account_id' => $data['cash_account_id'],
                'total_amount' => $totalAmount,
                'status' => 'posted',
                'created_by' => Auth::id(),
                'project_id' => $data['project_id'] ?? null,
                'fund_id' => $data['fund_id'] ?? null,
                'dept_id' => $data['dept_id'] ?? null,
            ]);

            // Create cash out lines
            foreach ($data['lines'] as $lineData) {
                $cashOut->lines()->create([
                    'account_id' => $lineData['account_id'],
                    'amount' => $lineData['amount'],
                    'memo' => $lineData['memo'] ?? null,
                    'project_id' => $lineData['project_id'] ?? null,
                    'fund_id' => $lineData['fund_id'] ?? null,
                    'dept_id' => $lineData['dept_id'] ?? null,
                ]);
            }

            // Create journal entries
            $journalLines = [];

            // Debit lines (expenses/assets)
            foreach ($data['lines'] as $lineData) {
                $journalLines[] = [
                    'account_id' => $lineData['account_id'],
                    'debit' => $lineData['amount'],
                    'credit' => 0,
                    'project_id' => $lineData['project_id'] ?? null,
                    'fund_id' => $lineData['fund_id'] ?? null,
                    'dept_id' => $lineData['dept_id'] ?? null,
                    'memo' => $lineData['memo'] ?? null,
                ];
            }

            // Credit line (cash account)
            $journalLines[] = [
                'account_id' => $data['cash_account_id'],
                'debit' => 0,
                'credit' => $totalAmount,
                'project_id' => $data['project_id'] ?? null,
                'fund_id' => $data['fund_id'] ?? null,
                'dept_id' => $data['dept_id'] ?? null,
                'memo' => $data['description'] ?? null,
            ];

            $this->posting->postJournal([
                'date' => $cashOut->date,
                'description' => 'Cash Out Voucher ' . $cashOut->voucher_number,
                'source_type' => 'cash_out',
                'source_id' => $cashOut->id,
                'status' => 'posted',
                'posted_by' => Auth::id(),
                'lines' => $journalLines,
            ]);

            return redirect()->route('banking.cash-out.index')->with('success', 'Cash out transaction posted successfully');
        });
    }

    public function data(Request $request)
    {
        $query = DB::table('cash_outs as co')
            ->join('accounts as ca', 'ca.id', '=', 'co.cash_account_id')
            ->join('users as u', 'u.id', '=', 'co.created_by')
            ->leftJoin('projects as p', 'p.id', '=', 'co.project_id')
            ->leftJoin('funds as f', 'f.id', '=', 'co.fund_id')
            ->leftJoin('departments as d', 'd.id', '=', 'co.dept_id')
            ->select(
                'co.id',
                'co.voucher_number',
                'co.date',
                'co.description',
                'co.total_amount',
                'co.status',
                'ca.code as cash_account_code',
                'ca.name as cash_account_name',
                'u.name as creator_name',
                'p.name as project_name',
                'f.name as fund_name',
                'd.name as department_name'
            );

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return date('d/m/Y', strtotime($row->date));
            })
            ->editColumn('total_amount', function ($row) {
                return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
            })
            ->editColumn('status', function ($row) {
                $badgeClass = $row->status === 'posted' ? 'badge-success' : 'badge-warning';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('cash_account', function ($row) {
                return $row->cash_account_code . ' - ' . $row->cash_account_name;
            })
            ->addColumn('dimensions', function ($row) {
                $dimensions = [];
                if ($row->project_name) $dimensions[] = 'P: ' . $row->project_name;
                if ($row->fund_name) $dimensions[] = 'F: ' . $row->fund_name;
                if ($row->department_name) $dimensions[] = 'D: ' . $row->department_name;
                return implode('<br>', $dimensions);
            })
            ->addColumn('actions', function ($row) {
                $actions = '<a href="/banking/cash-out/' . $row->id . '/print" target="_blank" class="btn btn-sm btn-info" title="Print"><i class="fas fa-print"></i></a>';
                return $actions;
            })
            ->rawColumns(['status', 'dimensions', 'actions'])
            ->toJson();
    }

    public function print(CashOut $cashOut)
    {
        $cashOut->load([
            'lines.account',
            'cashAccount',
            'creator',
            'project',
            'fund',
            'department'
        ]);

        // Convert amount to words (Indonesian)
        $terbilang = $this->convertToWords($cashOut->total_amount);

        return view('banking.cash_out.print', compact('cashOut', 'terbilang'));
    }

    private function convertToWords($number)
    {
        $ones = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
            'dua belas',
            'tiga belas',
            'empat belas',
            'lima belas',
            'enam belas',
            'tujuh belas',
            'delapan belas',
            'sembilan belas'
        ];

        $tens = [
            '',
            '',
            'dua puluh',
            'tiga puluh',
            'empat puluh',
            'lima puluh',
            'enam puluh',
            'tujuh puluh',
            'delapan puluh',
            'sembilan puluh'
        ];

        $hundreds = [
            '',
            'seratus',
            'dua ratus',
            'tiga ratus',
            'empat ratus',
            'lima ratus',
            'enam ratus',
            'tujuh ratus',
            'delapan ratus',
            'sembilan ratus'
        ];

        if ($number == 0) {
            return 'nol rupiah';
        }

        $result = '';
        $number = (int)$number;

        // Handle millions
        if ($number >= 1000000) {
            $millions = intval($number / 1000000);
            if ($millions == 1) {
                $result .= 'satu juta ';
            } else {
                $result .= $this->convertToWords($millions) . ' juta ';
            }
            $number %= 1000000;
        }

        // Handle thousands
        if ($number >= 1000) {
            $thousand = intval($number / 1000);
            if ($thousand == 1) {
                $result .= 'seribu ';
            } else {
                $result .= $this->convertToWords($thousand) . ' ribu ';
            }
            $number %= 1000;
        }

        // Handle hundreds
        if ($number >= 100) {
            $hundred = intval($number / 100);
            if ($hundred == 1) {
                $result .= 'seratus ';
            } else {
                $result .= $hundreds[$hundred] . ' ';
            }
            $number %= 100;
        }

        // Handle tens and ones
        if ($number >= 20) {
            $ten = intval($number / 10);
            $result .= $tens[$ten] . ' ';
            $number %= 10;
        } elseif ($number >= 10) {
            $result .= $ones[$number] . ' ';
            $number = 0;
        }

        if ($number > 0) {
            $result .= $ones[$number] . ' ';
        }

        return trim($result) . ' rupiah';
    }
}

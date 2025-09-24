<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Accounting\PurchaseInvoice;
use App\Models\Accounting\PurchaseInvoiceLine;
use App\Services\Accounting\PostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseInvoiceController extends Controller
{
    public function __construct(private PostingService $posting)
    {
        $this->middleware(['auth']);
        $this->middleware('permission:ap.invoices.view')->only(['index', 'show']);
        $this->middleware('permission:ap.invoices.create')->only(['create', 'store']);
        $this->middleware('permission:ap.invoices.post')->only(['post']);
    }

    public function index()
    {
        return view('purchase_invoices.index');
    }

    public function create()
    {
        $accounts = DB::table('accounts')->where('is_postable', 1)->orderBy('code')->get();
        $vendors = DB::table('vendors')->orderBy('name')->get();
        $items = DB::table('items')->where('is_active', 1)->orderBy('code')->get(['id', 'code', 'name', 'type']);
        $taxCodes = DB::table('tax_codes')->orderBy('code')->get();
        $projects = DB::table('projects')->orderBy('code')->get(['id', 'code', 'name']);
        $funds = DB::table('funds')->orderBy('code')->get(['id', 'code', 'name']);
        $departments = DB::table('departments')->orderBy('code')->get(['id', 'code', 'name']);
        return view('purchase_invoices.create', compact('accounts', 'vendors', 'items', 'taxCodes', 'projects', 'funds', 'departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.line_type' => ['required', 'in:item,service'],
            'lines.*.item_account_id' => ['required', 'integer'],
            'lines.*.description' => ['nullable', 'string', 'max:255'],
            'lines.*.qty' => ['required', 'numeric', 'min:0.01'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.vat_rate' => ['nullable', 'numeric', 'in:0,11'],
            'lines.*.wtax_rate' => ['nullable', 'numeric', 'in:0,2'],
            'lines.*.vat_amount' => ['required', 'numeric', 'min:0'],
            'lines.*.wtax_amount' => ['required', 'numeric', 'min:0'],
            'lines.*.amount' => ['required', 'numeric', 'min:0'],
            'lines.*.project_id' => ['nullable', 'integer'],
            'lines.*.fund_id' => ['nullable', 'integer'],
            'lines.*.dept_id' => ['nullable', 'integer'],
        ]);

        return DB::transaction(function () use ($data, $request) {
            $invoice = PurchaseInvoice::create([
                'invoice_no' => null,
                'date' => $data['date'],
                'vendor_id' => $data['vendor_id'],
                'purchase_order_id' => $request->input('purchase_order_id'),
                'goods_receipt_id' => $request->input('goods_receipt_id'),
                'description' => $data['description'] ?? null,
                'status' => 'draft',
                'total_amount' => 0,
            ]);

            $ym = date('Ym', strtotime($data['date']));
            $invoice->update(['invoice_no' => sprintf('PINV-%s-%06d', $ym, $invoice->id)]);

            $total = 0;
            foreach ($data['lines'] as $l) {
                $lineType = $l['line_type'];
                $itemAccountId = $l['item_account_id'];
                $itemId = null;
                $accountId = null;

                // Determine if it's an item or account based on line type
                if ($lineType === 'item') {
                    $itemId = $itemAccountId;
                    // For items, we need to get the default inventory account
                    $accountId = DB::table('items')->where('id', $itemId)->value('inventory_account_id') ?? 1; // Default to first account
                } else {
                    $accountId = $itemAccountId;
                }

                $amount = (float)$l['amount'];
                $total += $amount;

                PurchaseInvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'line_type' => $lineType,
                    'item_id' => $itemId,
                    'account_id' => $accountId,
                    'description' => $l['description'] ?? null,
                    'qty' => (float)$l['qty'],
                    'unit_price' => (float)$l['unit_price'],
                    'amount' => $amount,
                    'vat_amount' => (float)($l['vat_amount'] ?? 0),
                    'wtax_amount' => (float)($l['wtax_amount'] ?? 0),
                    'project_id' => $l['project_id'] ?? null,
                    'fund_id' => $l['fund_id'] ?? null,
                    'dept_id' => $l['dept_id'] ?? null,
                ]);
            }

            $termsDays = (int) ($request->input('terms_days') ?? 0);
            $dueDate = $termsDays > 0 ? date('Y-m-d', strtotime($data['date'] . ' +' . $termsDays . ' days')) : null;
            $invoice->update(['total_amount' => $total, 'terms_days' => $termsDays ?: null, 'due_date' => $dueDate]);
            return redirect()->route('purchase-invoices.show', $invoice->id)->with('success', 'Purchase invoice created');
        });
    }

    public function show(int $id)
    {
        $invoice = PurchaseInvoice::with('lines')->findOrFail($id);
        return view('purchase_invoices.show', compact('invoice'));
    }

    public function post(int $id)
    {
        $invoice = PurchaseInvoice::with('lines')->findOrFail($id);
        if ($invoice->status === 'posted') {
            return back()->with('success', 'Already posted');
        }

        $apAccountId = (int) DB::table('accounts')->where('code', '2.1.1')->value('id');
        $ppnInputId = (int) DB::table('accounts')->where('code', '1.1.6')->value('id');

        $expenseTotal = 0.0;
        $ppnTotal = 0.0;
        $withholdingTotal = 0.0;
        $lines = [];
        foreach ($invoice->lines as $l) {
            $expenseTotal += (float) $l->amount;

            // Use VAT amount from line instead of calculating from tax codes
            $ppnTotal += (float) $l->vat_amount;

            // Use WTax amount from line instead of calculating from tax codes
            $withholdingTotal += (float) $l->wtax_amount;

            $lines[] = [
                'account_id' => (int) $l->account_id,
                'debit' => (float) $l->amount,
                'credit' => 0,
                'project_id' => $l->project_id,
                'fund_id' => $l->fund_id,
                'dept_id' => $l->dept_id,
                'memo' => $l->description,
            ];
        }

        if ($ppnTotal > 0) {
            $lines[] = [
                'account_id' => $ppnInputId,
                'debit' => $ppnTotal,
                'credit' => 0,
                'project_id' => null,
                'fund_id' => null,
                'dept_id' => null,
                'memo' => 'PPN Masukan',
            ];
        }

        if ($withholdingTotal > 0) {
            $withholdingPayableId = (int) DB::table('accounts')->where('code', '2.1.3')->value('id');
            if ($withholdingPayableId) {
                $lines[] = [
                    'account_id' => $withholdingPayableId,
                    'debit' => 0,
                    'credit' => $withholdingTotal,
                    'project_id' => null,
                    'fund_id' => null,
                    'dept_id' => null,
                    'memo' => 'Withholding Tax Payable',
                ];
            }
        }

        $lines[] = [
            'account_id' => $apAccountId,
            'debit' => 0,
            'credit' => ($expenseTotal + $ppnTotal) - $withholdingTotal,
            'project_id' => null,
            'fund_id' => null,
            'dept_id' => null,
            'memo' => 'Accounts Payable',
        ];

        DB::transaction(function () use ($invoice, $lines) {
            $jid = $this->posting->postJournal([
                'date' => $invoice->date->toDateString(),
                'description' => 'Post AP Invoice #' . $invoice->id,
                'source_type' => 'purchase_invoice',
                'source_id' => $invoice->id,
                'lines' => $lines,
                'posted_by' => auth()->id(),
                'status' => 'posted',
            ]);

            $invoice->update(['status' => 'posted', 'posted_at' => now()]);
        });

        return back()->with('success', 'Purchase invoice posted');
    }

    public function print(int $id)
    {
        $invoice = PurchaseInvoice::with('lines')->findOrFail($id);
        return view('purchase_invoices.print', compact('invoice'));
    }

    public function pdf(int $id)
    {
        $invoice = PurchaseInvoice::with('lines')->findOrFail($id);
        $pdf = app(\App\Services\PdfService::class)->renderViewToString('purchase_invoices.print', [
            'invoice' => $invoice,
        ]);
        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="purchase-invoice-' . $id . '.pdf"'
        ]);
    }

    public function queuePdf(int $id)
    {
        $invoice = PurchaseInvoice::with('lines')->findOrFail($id);
        $path = 'public/pdfs/purchase-invoice-' . $invoice->id . '.pdf';
        \App\Jobs\GeneratePdfJob::dispatch('purchase_invoices.print', ['invoice' => $invoice], $path);
        $url = \Illuminate\Support\Facades\Storage::url($path);
        return back()->with('success', 'PDF generation started')->with('pdf_url', $url);
    }

    public function data(Request $request)
    {
        $q = DB::table('purchase_invoices as pi')
            ->leftJoin('vendors as v', 'v.id', '=', 'pi.vendor_id')
            ->select('pi.id', 'pi.date', 'pi.invoice_no', 'pi.vendor_id', 'v.name as vendor_name', 'pi.total_amount', 'pi.status');

        if ($request->filled('status')) {
            $q->where('pi.status', $request->input('status'));
        }
        if ($request->filled('from')) {
            $q->whereDate('pi.date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $q->whereDate('pi.date', '<=', $request->input('to'));
        }
        if ($request->filled('q')) {
            $kw = $request->input('q');
            $q->where(function ($w) use ($kw) {
                $w->where('pi.invoice_no', 'like', '%' . $kw . '%')
                    ->orWhere('pi.description', 'like', '%' . $kw . '%')
                    ->orWhere('v.name', 'like', '%' . $kw . '%');
            });
        }

        return DataTables::of($q)
            ->editColumn('total_amount', function ($row) {
                return number_format((float)$row->total_amount, 2);
            })
            ->editColumn('status', function ($row) {
                return strtoupper($row->status);
            })
            ->addColumn('vendor', function ($row) {
                return $row->vendor_name ?: ('#' . $row->vendor_id);
            })
            ->addColumn('actions', function ($row) {
                $url = route('purchase-invoices.show', $row->id);
                return '<a href="' . $url . '" class="btn btn-xs btn-info">View</a>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
}

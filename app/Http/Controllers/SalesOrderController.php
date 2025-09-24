<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\SalesOrderLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index()
    {
        return view('sales_orders.index');
    }

    public function create()
    {
        $customers = DB::table('customers')->orderBy('name')->get();
        $accounts = DB::table('accounts')->where('is_postable', 1)->orderBy('code')->get();
        $items = DB::table('items')->where('is_active', 1)->orderBy('code')->get(['id', 'code', 'name', 'type']);
        $taxCodes = DB::table('tax_codes')->orderBy('code')->get();
        return view('sales_orders.create', compact('customers', 'accounts', 'items', 'taxCodes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
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
        ]);

        return DB::transaction(function () use ($data) {
            $so = SalesOrder::create([
                'order_no' => null,
                'date' => $data['date'],
                'customer_id' => $data['customer_id'],
                'description' => $data['description'] ?? null,
                'status' => 'draft',
                'total_amount' => 0,
            ]);
            $ym = date('Ym', strtotime($data['date']));
            $so->update(['order_no' => sprintf('SO-%s-%06d', $ym, $so->id)]);
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

                SalesOrderLine::create([
                    'order_id' => $so->id,
                    'line_type' => $lineType,
                    'item_id' => $itemId,
                    'account_id' => $accountId,
                    'description' => $l['description'] ?? null,
                    'qty' => (float)$l['qty'],
                    'unit_price' => (float)$l['unit_price'],
                    'amount' => $amount,
                    'vat_amount' => (float)($l['vat_amount'] ?? 0),
                    'wtax_amount' => (float)($l['wtax_amount'] ?? 0),
                ]);
            }
            $so->update(['total_amount' => $total]);
            return redirect()->route('sales-orders.show', $so->id)->with('success', 'Sales Order created');
        });
    }

    public function show(int $id)
    {
        $order = SalesOrder::with('lines')->findOrFail($id);
        return view('sales_orders.show', compact('order'));
    }

    public function approve(int $id)
    {
        $order = SalesOrder::findOrFail($id);
        if ($order->status !== 'draft') {
            return back()->with('success', 'Already approved');
        }
        $order->update(['status' => 'approved']);
        return back()->with('success', 'Sales Order approved');
    }

    public function close(int $id)
    {
        $order = SalesOrder::findOrFail($id);
        if ($order->status === 'closed') {
            return back()->with('success', 'Already closed');
        }
        $order->update(['status' => 'closed']);
        return back()->with('success', 'Sales Order closed');
    }

    public function createInvoice(int $id)
    {
        $order = SalesOrder::with('lines')->findOrFail($id);
        $accounts = DB::table('accounts')->where('is_postable', 1)->orderBy('code')->get();
        $customers = DB::table('customers')->orderBy('name')->get();
        $items = DB::table('items')->where('is_active', 1)->orderBy('code')->get(['id', 'code', 'name', 'type']);
        $taxCodes = DB::table('tax_codes')->orderBy('code')->get();
        $projects = DB::table('projects')->orderBy('code')->get(['id', 'code', 'name']);
        $funds = DB::table('funds')->orderBy('code')->get(['id', 'code', 'name']);
        $departments = DB::table('departments')->orderBy('code')->get(['id', 'code', 'name']);
        $prefill = [
            'date' => now()->toDateString(),
            'customer_id' => $order->customer_id,
            'description' => 'From SO ' . ($order->order_no ?: ('#' . $order->id)),
            'lines' => $order->lines->map(function ($l) {
                return [
                    'line_type' => $l->line_type,
                    'item_account_id' => $l->line_type === 'item' ? $l->item_id : $l->account_id,
                    'description' => $l->description,
                    'qty' => (float)$l->qty,
                    'unit_price' => (float)$l->unit_price,
                    'vat_amount' => (float)$l->vat_amount,
                    'wtax_amount' => (float)$l->wtax_amount,
                    'amount' => (float)$l->amount,
                ];
            })->toArray(),
        ];
        return view('sales_invoices.create', compact('accounts', 'customers', 'items', 'taxCodes', 'projects', 'funds', 'departments') + ['prefill' => $prefill, 'sales_order_id' => $order->id]);
    }
}

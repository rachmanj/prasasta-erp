<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptLine;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends Controller
{
    public function index()
    {
        return view('goods_receipts.index');
    }

    public function create()
    {
        $vendors = DB::table('vendors')->orderBy('name')->get();
        $accounts = DB::table('accounts')->where('is_postable', 1)->orderBy('code')->get();
        $items = DB::table('items')->where('is_active', 1)->orderBy('code')->get(['id', 'code', 'name', 'type']);
        $taxCodes = DB::table('tax_codes')->orderBy('code')->get();
        $purchaseOrders = DB::table('purchase_orders')->orderByDesc('id')->limit(50)->get(['id', 'order_no']);
        return view('goods_receipts.create', compact('vendors', 'accounts', 'items', 'taxCodes', 'purchaseOrders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'vendor_id' => ['required', 'integer', 'exists:vendors,id'],
            'purchase_order_id' => ['nullable', 'integer', 'exists:purchase_orders,id'],
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
            $grn = GoodsReceipt::create([
                'grn_no' => null,
                'date' => $data['date'],
                'vendor_id' => $data['vendor_id'],
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'draft',
                'total_amount' => 0,
            ]);
            $ym = date('Ym', strtotime($data['date']));
            $grn->update(['grn_no' => sprintf('GR-%s-%06d', $ym, $grn->id)]);
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

                GoodsReceiptLine::create([
                    'grn_id' => $grn->id,
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
            $grn->update(['total_amount' => $total]);
            return redirect()->route('goods-receipts.show', $grn->id)->with('success', 'Goods Receipt created');
        });
    }

    public function show(int $id)
    {
        $grn = GoodsReceipt::with('lines')->findOrFail($id);
        return view('goods_receipts.show', compact('grn'));
    }

    public function receive(int $id)
    {
        $grn = GoodsReceipt::findOrFail($id);
        if ($grn->status === 'received') {
            return back()->with('success', 'Already received');
        }
        $grn->update(['status' => 'received']);
        return back()->with('success', 'Goods Receipt marked as received');
    }

    public function createInvoice(int $id)
    {
        $grn = GoodsReceipt::with('lines')->findOrFail($id);
        $accounts = DB::table('accounts')->where('is_postable', 1)->orderBy('code')->get();
        $vendors = DB::table('vendors')->orderBy('name')->get();
        $taxCodes = DB::table('tax_codes')->orderBy('code')->get();
        $prefill = [
            'date' => now()->toDateString(),
            'vendor_id' => $grn->vendor_id,
            'description' => 'From GRN ' . ($grn->grn_no ?: ('#' . $grn->id)),
            'lines' => $grn->lines->map(function ($l) {
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
        return view('purchase_invoices.create', compact('accounts', 'vendors', 'taxCodes') + ['prefill' => $prefill, 'goods_receipt_id' => $grn->id]);
    }
}

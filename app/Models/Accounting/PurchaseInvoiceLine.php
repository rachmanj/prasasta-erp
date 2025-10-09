<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceLine extends Model
{
    protected $table = 'purchase_invoice_lines';

    protected $fillable = [
        'invoice_id',
        'account_id',
        'item_id',
        'line_type',
        'description',
        'qty',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'amount',
        'vat_amount',
        'wtax_amount',
        'tax_code_id',
        'project_id',
        'fund_id',
        'dept_id',
    ];

    protected $casts = [
        'qty' => 'float',
        'unit_price' => 'float',
        'discount_percent' => 'float',
        'discount_amount' => 'float',
        'amount' => 'float',
        'vat_amount' => 'float',
        'wtax_amount' => 'float',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class, 'invoice_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Item::class, 'item_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}

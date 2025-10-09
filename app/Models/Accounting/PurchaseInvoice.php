<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseInvoice extends Model
{
    protected $table = 'purchase_invoices';

    protected $fillable = [
        'invoice_no',
        'reference_number',
        'date',
        'due_date',
        'terms_days',
        'vendor_id',
        'purchase_order_id',
        'goods_receipt_id',
        'description',
        'notes',
        'terms',
        'total_amount',
        'status',
        'posted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'posted_at' => 'datetime',
        'total_amount' => 'float',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceLine::class, 'invoice_id');
    }
}

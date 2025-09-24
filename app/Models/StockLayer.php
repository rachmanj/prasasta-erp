<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLayer extends Model
{
    protected $fillable = [
        'item_id',
        'purchase_date',
        'quantity',
        'unit_cost',
        'remaining_quantity',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'remaining_quantity' => 'decimal:4',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('remaining_quantity', '>', 0);
    }

    public function scopeForItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    public function scopeOldestFirst($query)
    {
        return $query->orderBy('purchase_date', 'asc')->orderBy('id', 'asc');
    }
}

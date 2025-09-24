<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentLine extends Model
{
    protected $fillable = [
        'adjustment_id',
        'item_id',
        'current_quantity',
        'adjusted_quantity',
        'variance_quantity',
        'unit_cost',
        'variance_value',
        'notes',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:4',
        'adjusted_quantity' => 'decimal:4',
        'variance_quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'variance_value' => 'decimal:2',
    ];

    public function adjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class, 'adjustment_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}

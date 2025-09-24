<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Accounting\Account;

class Item extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'barcode',
        'category_id',
        'inventory_account_id',
        'cost_of_goods_sold_account_id',
        'type',
        'unit_of_measure',
        'cost_method',
        'min_stock_level',
        'max_stock_level',
        'current_stock_quantity',
        'current_stock_value',
        'last_cost_price',
        'average_cost_price',
        'is_active',
    ];

    protected $casts = [
        'min_stock_level' => 'decimal:4',
        'max_stock_level' => 'decimal:4',
        'current_stock_quantity' => 'decimal:4',
        'current_stock_value' => 'decimal:2',
        'last_cost_price' => 'decimal:2',
        'average_cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function inventoryAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'inventory_account_id');
    }

    public function costOfGoodsSoldAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'cost_of_goods_sold_account_id');
    }

    public function stockLayers(): HasMany
    {
        return $this->hasMany(StockLayer::class, 'item_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeItems($query)
    {
        return $query->where('type', 'item');
    }

    public function scopeServices($query)
    {
        return $query->where('type', 'service');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock_quantity <= min_stock_level');
    }

    public function isLowStock(): bool
    {
        return $this->current_stock_quantity <= $this->min_stock_level;
    }

    public function isService(): bool
    {
        return $this->type === 'service';
    }

    public function isItem(): bool
    {
        return $this->type === 'item';
    }

    public function getAvailableStockAttribute(): float
    {
        return $this->current_stock_quantity;
    }
}

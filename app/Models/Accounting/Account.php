<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'is_postable',
        'parent_id',
        'control_type',
        'is_control_account',
        'description',
        'reconciliation_frequency',
        'tolerance_amount'
    ];

    protected $casts = [
        'is_postable' => 'boolean',
        'is_control_account' => 'boolean',
        'tolerance_amount' => 'decimal:2',
    ];

    /**
     * Relationship to ControlAccount
     */
    public function controlAccount()
    {
        return $this->hasOne(\App\Models\ControlAccount::class, 'code', 'code');
    }

    /**
     * Relationship to parent account
     */
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Relationship to child accounts
     */
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Relationship to journal lines
     */
    public function journalLines()
    {
        return $this->hasMany(\App\Models\Accounting\JournalLine::class, 'account_id');
    }

    /**
     * Scope for control accounts
     */
    public function scopeControlAccounts($query)
    {
        return $query->where('is_control_account', true);
    }

    /**
     * Scope for AP control accounts
     */
    public function scopeApControlAccounts($query)
    {
        return $query->where('is_control_account', true)->where('control_type', 'ap');
    }

    /**
     * Scope for AR control accounts
     */
    public function scopeArControlAccounts($query)
    {
        return $query->where('is_control_account', true)->where('control_type', 'ar');
    }

    /**
     * Check if account is a control account
     */
    public function isControlAccount(): bool
    {
        return $this->is_control_account;
    }

    /**
     * Get control account type label
     */
    public function getControlTypeLabel(): string
    {
        $labels = [
            'ap' => 'Accounts Payable',
            'ar' => 'Accounts Receivable',
            'cash' => 'Cash & Bank',
            'inventory' => 'Inventory',
            'fixed_assets' => 'Fixed Assets',
            'other' => 'Other'
        ];

        return $labels[$this->control_type] ?? 'Unknown';
    }

    /**
     * Check if any child accounts have journal transactions
     */
    public function hasChildrenWithTransactions(): bool
    {
        return $this->children()
            ->whereHas('journalLines')
            ->exists();
    }

    /**
     * Get child accounts that have journal transactions
     */
    public function getChildrenWithTransactions()
    {
        return $this->children()
            ->whereHas('journalLines')
            ->with('journalLines')
            ->get();
    }

    /**
     * Check if account can be updated (no children with transactions)
     */
    public function canBeUpdated(): bool
    {
        return !$this->hasChildrenWithTransactions();
    }
}

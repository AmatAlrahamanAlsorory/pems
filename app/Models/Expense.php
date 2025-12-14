<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'expense_number', 'project_id', 'location_id', 'custody_id', 'expense_category_id', 
        'expense_item_id', 'created_by', 'approved_by', 'amount', 'expense_date', 
        'vendor_name', 'invoice_number', 'invoice_file', 'description', 'status', 
        'rejection_reason', 'person_id', 'currency'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function custody(): BelongsTo
    {
        return $this->belongsTo(Custody::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ExpenseItem::class, 'expense_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function latestApproval()
    {
        return $this->morphOne(Approval::class, 'approvable')->latest();
    }

    public function needsApproval()
    {
        return $this->item->approval_level !== 'automatic';
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }
}

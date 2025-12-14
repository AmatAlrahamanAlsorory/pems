<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAllocation extends Model
{
    protected $fillable = [
        'project_id', 'expense_category_id', 'allocated_amount', 'spent_amount', 'percentage'
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function getRemainingAmountAttribute()
    {
        return $this->allocated_amount - $this->spent_amount;
    }

    public function getUsagePercentageAttribute()
    {
        return $this->allocated_amount > 0 ? ($this->spent_amount / $this->allocated_amount) * 100 : 0;
    }

    public function getStatusAttribute()
    {
        $usage = $this->usage_percentage;
        if ($usage >= 100) return 'exceeded';
        if ($usage >= 90) return 'critical';
        if ($usage >= 70) return 'warning';
        return 'normal';
    }
}
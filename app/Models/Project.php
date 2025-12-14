<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'total_budget', 'spent_amount', 'emergency_reserve',
        'planned_days', 'episodes_count', 'status', 'start_date', 'end_date', 'description'
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'emergency_reserve' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function custodies(): HasMany
    {
        return $this->hasMany(Custody::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function budgetAllocations(): HasMany
    {
        return $this->hasMany(BudgetAllocation::class);
    }
    
    public function periodicBudgets(): HasMany
    {
        return $this->hasMany(PeriodicBudget::class);
    }
    
    public function getActivePeriodicBudgetAttribute()
    {
        return $this->periodicBudgets()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    public function getRemainingBudgetAttribute()
    {
        return $this->total_budget - $this->spent_amount;
    }

    public function getBudgetPercentageAttribute()
    {
        return $this->total_budget > 0 ? ($this->spent_amount / $this->total_budget) * 100 : 0;
    }

    public function getBudgetStatusAttribute()
    {
        $percentage = $this->budget_percentage;
        if ($percentage >= 100) return 'critical';
        if ($percentage >= 90) return 'danger';
        if ($percentage >= 70) return 'warning';
        return 'normal';
    }

    protected static function booted()
    {
        static::updated(function ($project) {
            if ($project->wasChanged('spent_amount')) {
                app(\App\Services\NotificationService::class)->checkBudgetAlerts();
            }
        });
    }
}

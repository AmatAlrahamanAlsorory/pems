<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodicBudget extends Model
{
    protected $fillable = [
        'project_id', 'period_type', 'start_date', 'end_date',
        'total_budget', 'spent_amount', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_budget' => 'decimal:2',
        'spent_amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function allocations()
    {
        return $this->hasMany(BudgetAllocation::class, 'period_budget_id');
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
    
    public function isActive()
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }
}

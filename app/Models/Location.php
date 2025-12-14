<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'project_id', 'name', 'city', 'address', 'latitude', 'longitude', 'map_url',
        'budget_allocated', 'spent_amount', 'status'
    ];

    protected $casts = [
        'budget_allocated' => 'decimal:2',
        'spent_amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function custodies(): HasMany
    {
        return $this->hasMany(Custody::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getRemainingBudgetAttribute()
    {
        return $this->budget_allocated - $this->spent_amount;
    }

    public function getBudgetPercentageAttribute()
    {
        return $this->budget_allocated > 0 ? ($this->spent_amount / $this->budget_allocated) * 100 : 0;
    }
}
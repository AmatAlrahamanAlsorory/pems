<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Custody extends Model
{
    protected $fillable = [
        'custody_number', 'project_id', 'location_id', 'requested_by', 'approved_by', 
        'amount', 'currency', 'spent_amount', 'remaining_amount', 'status', 'request_date', 
        'approval_date', 'received_date', 'due_date', 'purpose', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function latestApproval()
    {
        return $this->morphOne(Approval::class, 'approvable')->latest();
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}

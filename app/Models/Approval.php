<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $fillable = [
        'approvable_type', 'approvable_id', 'user_id', 'approver_id', 
        'status', 'notes', 'approved_at'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function approve(User $approver, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approver_id' => $approver->id,
            'notes' => $notes,
            'approved_at' => now(),
        ]);
    }

    public function reject(User $approver, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'approver_id' => $approver->id,
            'notes' => $notes,
            'approved_at' => now(),
        ]);
    }
}
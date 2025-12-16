<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = ['name', 'type', 'position', 'phone', 'email', 'notes', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'actor' => 'ممثل',
            'director' => 'مخرج',
            'producer' => 'منتج',
            'crew' => 'طاقم فني',
            'other' => 'أخرى',
            default => 'غير محدد'
        };
    }
}
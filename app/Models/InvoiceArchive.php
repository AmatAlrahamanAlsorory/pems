<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceArchive extends Model
{
    protected $fillable = [
        'expense_id', 'file_path', 'original_name', 'file_size',
        'mime_type', 'extracted_data', 'metadata', 'archived_by', 'archived_at'
    ];

    protected $casts = [
        'extracted_data' => 'array',
        'metadata' => 'array',
        'archived_at' => 'datetime',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}

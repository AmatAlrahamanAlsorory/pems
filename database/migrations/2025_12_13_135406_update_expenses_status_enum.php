<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE expenses DROP CONSTRAINT IF EXISTS expenses_status_check");
        DB::statement("ALTER TABLE expenses ADD CONSTRAINT expenses_status_check CHECK (status IN ('pending', 'approved', 'rejected', 'confirmed'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE expenses DROP CONSTRAINT IF EXISTS expenses_status_check");
        DB::statement("ALTER TABLE expenses ADD CONSTRAINT expenses_status_check CHECK (status IN ('pending', 'approved', 'rejected'))");
    }
};
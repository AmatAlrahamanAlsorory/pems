<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة عمود الدور للمستخدمين
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'financial_manager',    // المدير المالي العام
                'admin_accountant',     // محاسب الإدارة
                'production_manager',   // مدير إنتاج الموقع
                'field_accountant',     // المحاسب الميداني
                'financial_assistant'   // مساعد مالي
            ])->default('financial_assistant');
            $table->string('location')->nullable(); // موقع العمل
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'location']);
        });
    }
};
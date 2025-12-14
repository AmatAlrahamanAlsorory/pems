<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول الميزانيات الفترية
        Schema::create('periodic_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->enum('period_type', ['weekly', 'monthly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_budget', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });
        
        // جدول تحويلات الميزانية
        Schema::create('budget_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_category_id')->constrained('expense_categories');
            $table->foreignId('to_category_id')->constrained('expense_categories');
            $table->decimal('amount', 15, 2);
            $table->text('reason');
            $table->foreignId('transferred_by')->constrained('users');
            $table->timestamp('transferred_at');
            $table->timestamps();
        });
        
        // جدول تعديلات الميزانية
        Schema::create('budget_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('expense_categories');
            $table->decimal('old_amount', 15, 2);
            $table->decimal('new_amount', 15, 2);
            $table->decimal('difference', 15, 2);
            $table->text('reason');
            $table->foreignId('adjusted_by')->constrained('users');
            $table->timestamp('adjusted_at');
            $table->timestamps();
        });
        
        // جدول أرشيف الفواتير
        Schema::create('invoice_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_name');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->json('extracted_data')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('archived_by')->constrained('users');
            $table->timestamp('archived_at');
            $table->timestamps();
            
            $table->index('archived_at');
            $table->index('expense_id');
        });
        
        // إضافة حقل period_budget_id لجدول budget_allocations
        if (Schema::hasTable('budget_allocations')) {
            Schema::table('budget_allocations', function (Blueprint $table) {
                if (!Schema::hasColumn('budget_allocations', 'period_budget_id')) {
                    $table->foreignId('period_budget_id')->nullable()->constrained('periodic_budgets')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_archives');
        Schema::dropIfExists('budget_adjustments');
        Schema::dropIfExists('budget_transfers');
        Schema::dropIfExists('periodic_budgets');
        
        if (Schema::hasTable('budget_allocations')) {
            Schema::table('budget_allocations', function (Blueprint $table) {
                if (Schema::hasColumn('budget_allocations', 'period_budget_id')) {
                    $table->dropForeign(['period_budget_id']);
                    $table->dropColumn('period_budget_id');
                }
            });
        }
    }
};

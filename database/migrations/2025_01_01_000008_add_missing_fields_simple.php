<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة حقول لجدول users
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'permissions')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('permissions')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true);
            });
        }
        if (!Schema::hasColumn('users', 'last_login')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login')->nullable();
            });
        }
        if (!Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        
        // إضافة حقول لجدول locations
        if (!Schema::hasColumn('locations', 'city')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('city')->nullable();
            });
        }
        if (!Schema::hasColumn('locations', 'address')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->text('address')->nullable();
            });
        }
        if (!Schema::hasColumn('locations', 'latitude')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable();
            });
        }
        if (!Schema::hasColumn('locations', 'longitude')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }
        if (!Schema::hasColumn('locations', 'status')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('status', 50)->default('active');
            });
        }
        if (!Schema::hasColumn('locations', 'project_id')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
        
        // إضافة حقول لجدول custodies
        if (!Schema::hasColumn('custodies', 'custody_number')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->string('custody_number')->nullable();
            });
        }
        if (!Schema::hasColumn('custodies', 'user_id')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
        if (!Schema::hasColumn('custodies', 'purpose')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->text('purpose')->nullable();
            });
        }
        if (!Schema::hasColumn('custodies', 'issued_date')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->date('issued_date')->nullable();
            });
        }
        if (!Schema::hasColumn('custodies', 'due_date')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->date('due_date')->nullable();
            });
        }
        if (!Schema::hasColumn('custodies', 'returned_amount')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->decimal('returned_amount', 15, 2)->default(0);
            });
        }
        if (!Schema::hasColumn('custodies', 'remaining_amount')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->decimal('remaining_amount', 15, 2)->default(0);
            });
        }
        if (!Schema::hasColumn('custodies', 'location')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->string('location')->nullable();
            });
        }
        if (!Schema::hasColumn('custodies', 'notes')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->text('notes')->nullable();
            });
        }
        if (!Schema::hasColumn('custodies', 'status')) {
            Schema::table('custodies', function (Blueprint $table) {
                $table->string('status', 50)->default('pending');
            });
        }
        
        // إضافة حقول لجدول notifications
        if (!Schema::hasColumn('notifications', 'title')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('title')->nullable();
            });
        }
        if (!Schema::hasColumn('notifications', 'message')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->text('message')->nullable();
            });
        }
        if (!Schema::hasColumn('notifications', 'type')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('type', 50)->default('info');
            });
        }
        if (!Schema::hasColumn('notifications', 'is_read')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->boolean('is_read')->default(false);
            });
        }
        if (!Schema::hasColumn('notifications', 'data')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->json('data')->nullable();
            });
        }
    }

    public function down(): void
    {
        // لا نحذف الحقول في rollback لتجنب فقدان البيانات
    }
};
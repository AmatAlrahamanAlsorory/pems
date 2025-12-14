<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custodies', function (Blueprint $table) {
            $table->enum('currency', ['YER', 'SAR', 'USD'])->default('YER')->after('amount');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->enum('currency', ['YER', 'SAR', 'USD'])->default('YER')->after('amount');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->enum('currency', ['YER', 'SAR', 'USD'])->default('YER')->after('total_budget');
        });
    }

    public function down(): void
    {
        Schema::table('custodies', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
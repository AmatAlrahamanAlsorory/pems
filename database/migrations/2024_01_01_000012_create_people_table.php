<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['actor', 'technician', 'crew']); // ممثل، فني، طاقم
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // إضافة person_id للمصروفات
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('person_id')->nullable()->constrained('people')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropColumn('person_id');
        });
        Schema::dropIfExists('people');
    }
};
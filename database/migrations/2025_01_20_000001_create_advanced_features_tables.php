<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Digital Signatures
        Schema::create('digital_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('document_path');
            $table->unsignedBigInteger('user_id');
            $table->text('signature_data');
            $table->string('hash');
            $table->enum('signature_type', ['certificate', 'pad'])->default('certificate');
            $table->timestamp('signed_at');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Blockchain Records
        Schema::create('blockchain_records', function (Blueprint $table) {
            $table->id();
            $table->integer('block_index');
            $table->string('block_hash');
            $table->string('previous_hash');
            $table->text('data');
            $table->bigInteger('timestamp');
            $table->integer('nonce');
            $table->timestamps();
        });

        // Video Analysis Reports
        Schema::create('video_analysis_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('video_path');
            $table->integer('scenes_count');
            $table->integer('actors_count');
            $table->json('equipment_list');
            $table->json('locations');
            $table->decimal('quality_score', 3, 2);
            $table->json('cost_breakdown');
            $table->json('recommendations');
            $table->timestamp('analysis_date');
            $table->timestamps();
            
            $table->foreign('project_id')->references('id')->on('projects');
        });

        // Employee Faces
        Schema::create('employee_faces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('person_id'); // Azure Face API person ID
            $table->string('photo_path');
            $table->timestamp('registered_at');
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('people');
        });

        // Attendance
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('location_id');
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->string('photo_path')->nullable();
            $table->decimal('confidence_score', 3, 2)->nullable();
            $table->enum('recognition_method', ['face', 'manual', 'card']);
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('people');
            $table->foreign('location_id')->references('id')->on('locations');
        });

        // Authentication Logs
        Schema::create('authentication_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('method'); // password, biometric, totp, etc.
            $table->boolean('success');
            $table->string('ip_address');
            $table->text('user_agent');
            $table->json('additional_data')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        // User Biometrics
        Schema::create('user_biometrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['fingerprint', 'face', 'voice']);
            $table->text('encrypted_data');
            $table->string('hash');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Blocked IPs
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('reason');
            $table->timestamp('blocked_until');
            $table->unsignedBigInteger('blocked_by');
            $table->timestamps();
            
            $table->foreign('blocked_by')->references('id')->on('users');
        });

        // User Consent (GDPR)
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('consent_type');
            $table->boolean('granted');
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->string('ip_address');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Performance Metrics
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name');
            $table->decimal('value', 10, 4);
            $table->string('unit');
            $table->json('metadata')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // External Integration Logs
        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('system'); // SAP, Oracle, Bank, etc.
            $table->string('operation');
            $table->json('request_data');
            $table->json('response_data')->nullable();
            $table->boolean('success');
            $table->string('error_message')->nullable();
            $table->timestamps();
        });

        // Advanced Reports
        Schema::create('advanced_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->string('title');
            $table->json('parameters');
            $table->json('data');
            $table->unsignedBigInteger('generated_by');
            $table->timestamp('generated_at');
            $table->timestamps();
            
            $table->foreign('generated_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('advanced_reports');
        Schema::dropIfExists('integration_logs');
        Schema::dropIfExists('performance_metrics');
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('blocked_ips');
        Schema::dropIfExists('user_biometrics');
        Schema::dropIfExists('authentication_logs');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employee_faces');
        Schema::dropIfExists('video_analysis_reports');
        Schema::dropIfExists('blockchain_records');
        Schema::dropIfExists('digital_signatures');
    }
};
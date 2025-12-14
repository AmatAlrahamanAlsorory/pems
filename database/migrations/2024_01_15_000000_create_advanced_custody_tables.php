<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // جدول قواعد العهد المتقدمة
        Schema::create('custody_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_type'); // max_open, settlement_percentage, ceiling, etc.
            $table->json('conditions'); // شروط تطبيق القاعدة
            $table->json('parameters'); // معاملات القاعدة
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(1);
            $table->timestamps();
        });

        // جدول الميزانيات الفترية
        Schema::create('period_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('period_type'); // weekly, monthly, phase
            $table->integer('period_number');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->decimal('committed_amount', 15, 2)->default(0);
            $table->json('category_limits'); // حدود الفئات
            $table->json('adjustment_factors'); // عوامل التعديل
            $table->boolean('auto_adjustments')->default(true);
            $table->boolean('rollover_allowed')->default(true);
            $table->decimal('rollover_amount', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // جدول التنبؤات المالية
        Schema::create('financial_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('forecast_type'); // expense, budget_completion, cash_flow
            $table->integer('forecast_horizon_weeks');
            $table->json('predictions'); // التنبؤات المفصلة
            $table->json('confidence_intervals'); // فترات الثقة
            $table->json('scenario_analysis'); // تحليل السيناريوهات
            $table->decimal('accuracy_score', 5, 2)->nullable();
            $table->json('model_parameters'); // معاملات النموذج
            $table->timestamp('forecast_date');
            $table->timestamps();
        });

        // جدول تحليل المخاطر المتقدم
        Schema::create('advanced_risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('risk_category'); // financial, operational, technical
            $table->string('risk_type');
            $table->decimal('probability', 5, 2); // 0-100%
            $table->decimal('impact_score', 5, 2); // 1-10
            $table->decimal('overall_risk_score', 5, 2);
            $table->json('risk_factors'); // العوامل المساهمة
            $table->json('mitigation_strategies'); // استراتيجيات التخفيف
            $table->json('monitoring_indicators'); // مؤشرات المراقبة
            $table->string('status')->default('active');
            $table->timestamp('assessment_date');
            $table->timestamps();
        });

        // جدول الأداء التنبؤي
        Schema::create('performance_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('metric_type'); // kpi, quality, efficiency
            $table->string('metric_name');
            $table->decimal('current_value', 10, 4);
            $table->decimal('predicted_value', 10, 4);
            $table->decimal('confidence_level', 5, 2);
            $table->json('trend_analysis'); // تحليل الاتجاه
            $table->json('influencing_factors'); // العوامل المؤثرة
            $table->date('prediction_date');
            $table->integer('prediction_horizon_days');
            $table->timestamps();
        });

        // جدول التعديلات التلقائية
        Schema::create('automatic_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('adjustment_type'); // budget_reallocation, timeline, resource
            $table->json('trigger_conditions'); // شروط التفعيل
            $table->json('adjustment_parameters'); // معاملات التعديل
            $table->json('before_state'); // الحالة قبل التعديل
            $table->json('after_state'); // الحالة بعد التعديل
            $table->decimal('impact_amount', 15, 2)->nullable();
            $table->string('approval_status')->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();
        });

        // جدول الذكاء السوقي
        Schema::create('market_intelligence', function (Blueprint $table) {
            $table->id();
            $table->string('industry_sector');
            $table->string('metric_type'); // benchmark, trend, competitive
            $table->string('metric_name');
            $table->decimal('metric_value', 15, 4);
            $table->json('data_sources'); // مصادر البيانات
            $table->json('analysis_parameters'); // معاملات التحليل
            $table->date('data_date');
            $table->string('reliability_score');
            $table->timestamps();
        });

        // جدول النماذج التنبؤية
        Schema::create('predictive_models', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->string('model_type'); // linear, neural_network, ensemble
            $table->string('target_variable'); // expense, timeline, quality
            $table->json('input_features'); // المتغيرات المدخلة
            $table->json('model_parameters'); // معاملات النموذج
            $table->json('training_data_summary'); // ملخص بيانات التدريب
            $table->decimal('accuracy_score', 5, 2);
            $table->decimal('validation_score', 5, 2);
            $table->json('performance_metrics'); // مقاييس الأداء
            $table->timestamp('last_trained_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول التنبيهات الذكية
        Schema::create('intelligent_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('alert_type'); // predictive, threshold, anomaly
            $table->string('severity'); // low, medium, high, critical
            $table->string('category'); // budget, timeline, quality, risk
            $table->text('message');
            $table->json('alert_data'); // بيانات التنبيه
            $table->json('recommended_actions'); // الإجراءات المقترحة
            $table->decimal('confidence_score', 5, 2);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // جدول تحسين الأداء
        Schema::create('performance_optimizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('optimization_type'); // cost, time, quality, resource
            $table->text('description');
            $table->json('current_metrics'); // المقاييس الحالية
            $table->json('target_metrics'); // المقاييس المستهدفة
            $table->json('optimization_steps'); // خطوات التحسين
            $table->decimal('expected_savings', 15, 2)->nullable();
            $table->integer('expected_time_reduction_days')->nullable();
            $table->decimal('implementation_cost', 15, 2)->nullable();
            $table->decimal('roi_percentage', 5, 2)->nullable();
            $table->string('status')->default('proposed');
            $table->timestamp('implemented_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_optimizations');
        Schema::dropIfExists('intelligent_alerts');
        Schema::dropIfExists('predictive_models');
        Schema::dropIfExists('market_intelligence');
        Schema::dropIfExists('automatic_adjustments');
        Schema::dropIfExists('performance_predictions');
        Schema::dropIfExists('advanced_risk_assessments');
        Schema::dropIfExists('financial_forecasts');
        Schema::dropIfExists('period_budgets');
        Schema::dropIfExists('custody_rules');
    }
};
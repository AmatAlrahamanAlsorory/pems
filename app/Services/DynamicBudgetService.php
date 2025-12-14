<?php

namespace App\Services;

use App\Models\Project;
use App\Models\PeriodBudget;
use Carbon\Carbon;

class DynamicBudgetService
{
    // الميزانيات الفترية الذكية
    public function createSmartPeriodBudgets($projectId, $config)
    {
        $project = Project::find($projectId);
        
        return [
            'weekly_budgets' => $this->createWeeklyBudgets($project, $config),
            'monthly_budgets' => $this->createMonthlyBudgets($project, $config),
            'phase_budgets' => $this->createPhaseBudgets($project, $config),
            'adaptive_rules' => $this->setupAdaptiveRules($project),
            'auto_adjustments' => $this->configureAutoAdjustments($project)
        ];
    }

    private function createWeeklyBudgets($project, $config)
    {
        $totalWeeks = $this->calculateProjectWeeks($project);
        $weeklyBudgets = [];
        
        for ($week = 1; $week <= $totalWeeks; $week++) {
            $baseAmount = $project->budget / $totalWeeks;
            
            // عوامل التعديل الذكية
            $productionIntensity = $this->getWeeklyProductionIntensity($project, $week);
            $seasonalFactor = $this->getSeasonalFactor($project, $week);
            $resourceAvailability = $this->getResourceAvailability($project, $week);
            $historicalPattern = $this->getHistoricalPattern($project, $week);
            
            $adjustedAmount = $baseAmount * $productionIntensity * $seasonalFactor * $resourceAvailability * $historicalPattern;
            
            $weeklyBudgets[$week] = [
                'week_number' => $week,
                'base_amount' => $baseAmount,
                'adjusted_amount' => round($adjustedAmount),
                'factors' => [
                    'production_intensity' => $productionIntensity,
                    'seasonal_factor' => $seasonalFactor,
                    'resource_availability' => $resourceAvailability,
                    'historical_pattern' => $historicalPattern
                ],
                'category_distribution' => $this->calculateWeeklyCategoryDistribution($adjustedAmount, $week),
                'flexibility_buffer' => round($adjustedAmount * 0.1), // 10% مرونة
                'auto_rollover' => $week < $totalWeeks
            ];
        }

        return $weeklyBudgets;
    }

    private function createMonthlyBudgets($project, $config)
    {
        $projectMonths = $this->calculateProjectMonths($project);
        $monthlyBudgets = [];
        
        foreach ($projectMonths as $monthIndex => $month) {
            $baseAmount = $project->budget / count($projectMonths);
            
            // تحليل متقدم للشهر
            $monthAnalysis = $this->analyzeMonth($project, $month);
            $adjustmentFactor = $this->calculateMonthlyAdjustment($monthAnalysis);
            
            $adjustedAmount = $baseAmount * $adjustmentFactor;
            
            $monthlyBudgets[$monthIndex] = [
                'month' => $month['name'],
                'start_date' => $month['start'],
                'end_date' => $month['end'],
                'base_amount' => $baseAmount,
                'adjusted_amount' => round($adjustedAmount),
                'analysis' => $monthAnalysis,
                'weekly_breakdown' => $this->breakdownMonthToWeeks($adjustedAmount, $month),
                'category_limits' => $this->setMonthlyCategoryLimits($adjustedAmount),
                'contingency_reserve' => round($adjustedAmount * 0.15), // 15% احتياطي
                'performance_targets' => $this->setMonthlyTargets($project, $adjustedAmount)
            ];
        }

        return $monthlyBudgets;
    }

    private function createPhaseBudgets($project, $config)
    {
        $phases = $this->identifyProjectPhases($project);
        $phaseBudgets = [];
        
        foreach ($phases as $phaseIndex => $phase) {
            $phaseComplexity = $this->calculatePhaseComplexity($phase);
            $resourceRequirements = $this->calculatePhaseResources($phase);
            $riskLevel = $this->assessPhaseRisk($phase);
            
            $phaseAmount = $this->calculatePhaseAmount($project->budget, $phase, $phaseComplexity, $resourceRequirements);
            
            $phaseBudgets[$phaseIndex] = [
                'phase_name' => $phase['name'],
                'description' => $phase['description'],
                'duration_weeks' => $phase['duration'],
                'allocated_amount' => $phaseAmount,
                'complexity_score' => $phaseComplexity,
                'resource_requirements' => $resourceRequirements,
                'risk_assessment' => $riskLevel,
                'milestone_budgets' => $this->createMilestoneBudgets($phaseAmount, $phase),
                'quality_gates' => $this->defineQualityGates($phase),
                'approval_thresholds' => $this->setApprovalThresholds($phaseAmount, $riskLevel)
            ];
        }

        return $phaseBudgets;
    }

    // التعديل التلقائي الذكي
    public function performIntelligentAdjustments($projectId)
    {
        $project = Project::find($projectId);
        $currentPerformance = $this->analyzeCurrentPerformance($project);
        
        $adjustments = [
            'budget_reallocation' => $this->suggestBudgetReallocation($project, $currentPerformance),
            'timeline_adjustments' => $this->suggestTimelineAdjustments($project, $currentPerformance),
            'resource_optimization' => $this->optimizeResourceAllocation($project, $currentPerformance),
            'risk_mitigation' => $this->suggestRiskMitigation($project, $currentPerformance)
        ];

        // تطبيق التعديلات المعتمدة تلقائياً
        $this->applyAutomaticAdjustments($project, $adjustments);
        
        return $adjustments;
    }

    private function suggestBudgetReallocation($project, $performance)
    {
        $suggestions = [];
        $categories = [100, 200, 300, 400, 500, 600, 700, 800, 900];
        
        foreach ($categories as $category) {
            $categoryPerformance = $performance['categories'][$category] ?? [];
            $utilizationRate = $categoryPerformance['utilization_rate'] ?? 0;
            $trendDirection = $categoryPerformance['trend'] ?? 'stable';
            
            if ($utilizationRate < 60 && $trendDirection === 'declining') {
                $excessAmount = $categoryPerformance['allocated'] * (1 - $utilizationRate / 100);
                $suggestions[] = [
                    'type' => 'reduce',
                    'from_category' => $category,
                    'amount' => round($excessAmount * 0.3), // إعادة توزيع 30% من الفائض
                    'reason' => 'انخفاض معدل الاستخدام والاتجاه التنازلي',
                    'confidence' => 0.8
                ];
            }
            
            if ($utilizationRate > 90 && $trendDirection === 'increasing') {
                $additionalNeed = $categoryPerformance['allocated'] * 0.2;
                $suggestions[] = [
                    'type' => 'increase',
                    'to_category' => $category,
                    'amount' => round($additionalNeed),
                    'reason' => 'ارتفاع معدل الاستخدام والطلب المتزايد',
                    'confidence' => 0.9
                ];
            }
        }

        return $this->optimizeReallocationSuggestions($suggestions);
    }

    // التنبؤات المتقدمة
    public function generateAdvancedForecasts($projectId, $forecastHorizon = 12)
    {
        $project = Project::find($projectId);
        
        return [
            'expense_forecasts' => $this->forecastExpenses($project, $forecastHorizon),
            'budget_variance_predictions' => $this->predictBudgetVariances($project, $forecastHorizon),
            'cash_flow_projections' => $this->projectCashFlow($project, $forecastHorizon),
            'resource_demand_forecasts' => $this->forecastResourceDemand($project, $forecastHorizon),
            'risk_evolution_predictions' => $this->predictRiskEvolution($project, $forecastHorizon),
            'completion_scenarios' => $this->generateCompletionScenarios($project),
            'optimization_opportunities' => $this->identifyOptimizationOpportunities($project, $forecastHorizon)
        ];
    }

    private function forecastExpenses($project, $horizon)
    {
        $forecasts = [];
        $currentTrends = $this->analyzeCurrentTrends($project);
        
        for ($week = 1; $week <= $horizon; $week++) {
            $baseExpense = $this->calculateBaseWeeklyExpense($project);
            
            // عوامل التنبؤ المتقدمة
            $trendFactor = $this->applyTrendFactor($currentTrends, $week);
            $seasonalFactor = $this->applySeasonalFactor($project, $week);
            $cyclicalFactor = $this->applyCyclicalFactor($project, $week);
            $externalFactor = $this->applyExternalFactors($project, $week);
            
            $predictedExpense = $baseExpense * $trendFactor * $seasonalFactor * $cyclicalFactor * $externalFactor;
            
            $forecasts["week_$week"] = [
                'predicted_amount' => round($predictedExpense),
                'confidence_interval' => $this->calculateConfidenceInterval($predictedExpense, $week),
                'factors' => [
                    'trend' => $trendFactor,
                    'seasonal' => $seasonalFactor,
                    'cyclical' => $cyclicalFactor,
                    'external' => $externalFactor
                ],
                'scenario_analysis' => $this->generateScenarioAnalysis($predictedExpense, $week)
            ];
        }

        return $forecasts;
    }

    // الذكاء الاصطناعي للميزانيات
    public function applyAIOptimization($projectId)
    {
        $project = Project::find($projectId);
        
        return [
            'ml_budget_optimization' => $this->machineLearningOptimization($project),
            'pattern_recognition' => $this->recognizeSpendingPatterns($project),
            'anomaly_detection' => $this->detectBudgetAnomalies($project),
            'predictive_alerts' => $this->generatePredictiveAlerts($project),
            'automated_recommendations' => $this->generateAutomatedRecommendations($project)
        ];
    }

    private function machineLearningOptimization($project)
    {
        // خوارزمية تحسين الميزانية بالذكاء الاصطناعي
        $historicalData = $this->prepareMLData($project);
        $model = $this->trainBudgetModel($historicalData);
        
        return [
            'optimal_allocation' => $model->predictOptimalAllocation(),
            'efficiency_score' => $model->calculateEfficiencyScore(),
            'improvement_suggestions' => $model->generateImprovements(),
            'risk_predictions' => $model->predictRisks(),
            'model_confidence' => $model->getConfidenceScore()
        ];
    }

    // وظائف مساعدة
    private function calculateProjectWeeks($project)
    {
        $start = Carbon::parse($project->start_date);
        $end = Carbon::parse($project->end_date);
        return $start->diffInWeeks($end);
    }

    private function getWeeklyProductionIntensity($project, $week)
    {
        // منطق حساب كثافة الإنتاج الأسبوعية
        $totalWeeks = $this->calculateProjectWeeks($project);
        $midPoint = $totalWeeks / 2;
        
        // منحنى الإنتاج: بطيء في البداية، ذروة في المنتصف، تباطؤ في النهاية
        if ($week <= $midPoint) {
            return 0.7 + (0.6 * ($week / $midPoint));
        } else {
            return 1.3 - (0.6 * (($week - $midPoint) / ($totalWeeks - $midPoint)));
        }
    }

    private function getSeasonalFactor($project, $week)
    {
        // عوامل موسمية حسب نوع الإنتاج
        $seasonalPatterns = [
            'drama' => [1.2, 1.1, 1.0, 0.9, 0.8, 0.9, 1.0, 1.1],
            'comedy' => [1.0, 1.1, 1.2, 1.1, 1.0, 0.9, 1.0, 1.1],
            'documentary' => [1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0, 1.0]
        ];
        
        $pattern = $seasonalPatterns[$project->type] ?? $seasonalPatterns['drama'];
        $seasonIndex = ($week - 1) % count($pattern);
        
        return $pattern[$seasonIndex];
    }
}
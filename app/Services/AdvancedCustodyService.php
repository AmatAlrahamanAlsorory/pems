<?php

namespace App\Services;

use App\Models\Custody;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class AdvancedCustodyService
{
    // قواعد العهد المتقدمة
    public function validateCustodyRequest($userId, $projectId, $amount, $locationId = null)
    {
        $rules = [
            'max_open_custodies' => $this->checkMaxOpenCustodies($userId),
            'settlement_percentage' => $this->checkSettlementPercentage($userId),
            'custody_ceiling' => $this->checkCustodyCeiling($amount, $projectId, $locationId),
            'time_restrictions' => $this->checkTimeRestrictions($userId),
            'budget_availability' => $this->checkBudgetAvailability($projectId, $amount),
            'user_performance' => $this->checkUserPerformance($userId),
            'risk_assessment' => $this->assessRisk($userId, $amount)
        ];

        return [
            'allowed' => !in_array(false, $rules),
            'violations' => array_keys(array_filter($rules, fn($v) => $v === false)),
            'warnings' => $this->generateWarnings($rules),
            'recommendations' => $this->generateRecommendations($rules, $amount)
        ];
    }

    private function checkMaxOpenCustodies($userId)
    {
        $openCount = Custody::where('user_id', $userId)
            ->whereIn('status', ['approved', 'active'])
            ->count();
        
        $userLevel = User::find($userId)->level ?? 'basic';
        $limits = [
            'basic' => 2,
            'senior' => 3,
            'manager' => 5,
            'director' => 10
        ];

        return $openCount < ($limits[$userLevel] ?? 2);
    }

    private function checkSettlementPercentage($userId)
    {
        $activeCustodies = Custody::where('user_id', $userId)
            ->where('status', 'active')
            ->get();

        foreach ($activeCustodies as $custody) {
            $settledAmount = $custody->expenses()->sum('amount');
            $settlementPercentage = ($settledAmount / $custody->amount) * 100;
            
            if ($settlementPercentage < 80) {
                return false;
            }
        }

        return true;
    }

    private function checkCustodyCeiling($amount, $projectId, $locationId)
    {
        $project = Project::find($projectId);
        $baseCeiling = $project->custody_ceiling ?? 5000000;
        
        // تعديل السقف حسب الموقع
        if ($locationId) {
            $locationMultiplier = $this->getLocationMultiplier($locationId);
            $baseCeiling *= $locationMultiplier;
        }

        return $amount <= $baseCeiling;
    }

    private function checkTimeRestrictions($userId)
    {
        $lastCustody = Custody::where('user_id', $userId)
            ->latest()
            ->first();

        if (!$lastCustody) return true;

        $hoursSinceLastRequest = Carbon::now()->diffInHours($lastCustody->created_at);
        return $hoursSinceLastRequest >= 24; // حد أدنى 24 ساعة بين الطلبات
    }

    private function checkBudgetAvailability($projectId, $amount)
    {
        $project = Project::find($projectId);
        $totalSpent = $project->expenses()->sum('amount');
        $availableBudget = $project->budget - $totalSpent;
        
        return $availableBudget >= $amount;
    }

    private function checkUserPerformance($userId)
    {
        $user = User::find($userId);
        $recentCustodies = Custody::where('user_id', $userId)
            ->where('created_at', '>=', Carbon::now()->subMonths(3))
            ->get();

        $performanceScore = 100;
        
        foreach ($recentCustodies as $custody) {
            // خصم نقاط للتأخير في التصفية
            if ($custody->settlement_delay_days > 7) {
                $performanceScore -= 10;
            }
            
            // خصم نقاط للمخالفات
            if ($custody->violations_count > 0) {
                $performanceScore -= ($custody->violations_count * 5);
            }
        }

        return $performanceScore >= 70;
    }

    private function assessRisk($userId, $amount)
    {
        $riskFactors = [
            'amount_risk' => $amount > 10000000 ? 'high' : ($amount > 5000000 ? 'medium' : 'low'),
            'user_history' => $this->getUserRiskHistory($userId),
            'time_of_request' => $this->getTimeRisk(),
            'frequency_risk' => $this->getFrequencyRisk($userId)
        ];

        $highRiskCount = count(array_filter($riskFactors, fn($risk) => $risk === 'high'));
        
        return $highRiskCount <= 1; // السماح بعامل خطر واحد فقط
    }

    // الميزانيات الفترية الديناميكية
    public function createPeriodicBudgets($projectId, $totalBudget, $periods)
    {
        $project = Project::find($projectId);
        $budgetDistribution = $this->calculateOptimalDistribution($project, $totalBudget, $periods);
        
        foreach ($periods as $index => $period) {
            $this->createPeriodBudget([
                'project_id' => $projectId,
                'period_number' => $index + 1,
                'start_date' => $period['start'],
                'end_date' => $period['end'],
                'allocated_amount' => $budgetDistribution[$index],
                'category_limits' => $this->calculateCategoryLimits($budgetDistribution[$index]),
                'auto_adjustments' => true,
                'rollover_allowed' => $period['rollover'] ?? true
            ]);
        }

        return $budgetDistribution;
    }

    private function calculateOptimalDistribution($project, $totalBudget, $periods)
    {
        $distribution = [];
        $seasonalFactors = $this->getSeasonalFactors($project->type);
        $productionIntensity = $this->getProductionIntensity($project);
        
        foreach ($periods as $index => $period) {
            $basePortion = $totalBudget / count($periods);
            $seasonalAdjustment = $seasonalFactors[$index] ?? 1.0;
            $intensityAdjustment = $productionIntensity[$index] ?? 1.0;
            
            $adjustedAmount = $basePortion * $seasonalAdjustment * $intensityAdjustment;
            $distribution[] = round($adjustedAmount);
        }

        // تعديل للتأكد من المجموع الصحيح
        $totalDistributed = array_sum($distribution);
        $difference = $totalBudget - $totalDistributed;
        $distribution[0] += $difference;

        return $distribution;
    }

    // التحليلات التنبؤية الكاملة
    public function generatePredictiveAnalytics($projectId, $analysisType = 'comprehensive')
    {
        $project = Project::find($projectId);
        $historicalData = $this->getHistoricalData($project);
        
        return [
            'budget_forecast' => $this->predictBudgetConsumption($project, $historicalData),
            'expense_trends' => $this->analyzeExpenseTrends($project, $historicalData),
            'risk_predictions' => $this->predictRisks($project, $historicalData),
            'optimization_suggestions' => $this->generateOptimizationSuggestions($project),
            'seasonal_adjustments' => $this->predictSeasonalImpacts($project),
            'completion_forecast' => $this->predictProjectCompletion($project),
            'cost_overrun_probability' => $this->calculateOverrunProbability($project),
            'resource_allocation_optimization' => $this->optimizeResourceAllocation($project)
        ];
    }

    private function predictBudgetConsumption($project, $historicalData)
    {
        $currentSpendRate = $this->calculateCurrentSpendRate($project);
        $remainingDays = $this->getRemainingProjectDays($project);
        $seasonalFactors = $this->getUpcomingSeasonalFactors($project);
        
        $predictions = [];
        for ($week = 1; $week <= min(12, ceil($remainingDays / 7)); $week++) {
            $baseConsumption = $currentSpendRate * 7;
            $seasonalAdjustment = $seasonalFactors[$week] ?? 1.0;
            $trendAdjustment = $this->getTrendAdjustment($project, $week);
            
            $predictedConsumption = $baseConsumption * $seasonalAdjustment * $trendAdjustment;
            $predictions["week_$week"] = [
                'predicted_amount' => round($predictedConsumption),
                'confidence_level' => $this->calculateConfidence($project, $week),
                'risk_factors' => $this->identifyWeeklyRisks($project, $week)
            ];
        }

        return $predictions;
    }

    private function analyzeExpenseTrends($project, $historicalData)
    {
        $categories = [100, 200, 300, 400, 500, 600, 700, 800, 900];
        $trends = [];

        foreach ($categories as $category) {
            $categoryExpenses = $project->expenses()
                ->where('category_code', 'like', $category . '%')
                ->orderBy('created_at')
                ->get();

            $trends[$category] = [
                'current_trend' => $this->calculateTrend($categoryExpenses),
                'predicted_next_month' => $this->predictCategorySpending($categoryExpenses),
                'anomaly_detection' => $this->detectAnomalies($categoryExpenses),
                'optimization_potential' => $this->calculateOptimizationPotential($categoryExpenses)
            ];
        }

        return $trends;
    }

    private function predictRisks($project, $historicalData)
    {
        return [
            'budget_overrun_risk' => $this->calculateOverrunRisk($project),
            'timeline_delay_risk' => $this->calculateDelayRisk($project),
            'quality_compromise_risk' => $this->calculateQualityRisk($project),
            'resource_shortage_risk' => $this->calculateResourceRisk($project),
            'external_factor_risks' => $this->assessExternalRisks($project),
            'mitigation_strategies' => $this->generateMitigationStrategies($project)
        ];
    }

    private function generateOptimizationSuggestions($project)
    {
        $suggestions = [];
        
        // تحليل الكفاءة
        $efficiencyAnalysis = $this->analyzeEfficiency($project);
        if ($efficiencyAnalysis['improvement_potential'] > 15) {
            $suggestions[] = [
                'type' => 'efficiency',
                'priority' => 'high',
                'description' => 'إمكانية تحسين الكفاءة بنسبة ' . $efficiencyAnalysis['improvement_potential'] . '%',
                'actions' => $efficiencyAnalysis['recommended_actions'],
                'expected_savings' => $efficiencyAnalysis['potential_savings']
            ];
        }

        // تحليل إعادة التوزيع
        $reallocationOpportunities = $this->findReallocationOpportunities($project);
        foreach ($reallocationOpportunities as $opportunity) {
            $suggestions[] = [
                'type' => 'reallocation',
                'priority' => $opportunity['priority'],
                'description' => $opportunity['description'],
                'from_category' => $opportunity['from'],
                'to_category' => $opportunity['to'],
                'amount' => $opportunity['amount']
            ];
        }

        return $suggestions;
    }

    // وظائف مساعدة
    private function getLocationMultiplier($locationId)
    {
        $locationFactors = [
            'remote' => 1.5,
            'urban' => 1.0,
            'international' => 2.0,
            'studio' => 0.8
        ];

        // منطق تحديد نوع الموقع
        return $locationFactors['urban']; // افتراضي
    }

    private function generateWarnings($rules)
    {
        $warnings = [];
        
        if (!$rules['settlement_percentage']) {
            $warnings[] = 'يجب تصفية 80% من العهد الحالية قبل طلب جديدة';
        }
        
        if (!$rules['user_performance']) {
            $warnings[] = 'أداء المستخدم أقل من المستوى المطلوب';
        }

        return $warnings;
    }

    private function generateRecommendations($rules, $amount)
    {
        $recommendations = [];
        
        if (!$rules['custody_ceiling']) {
            $recommendations[] = 'تقليل المبلغ المطلوب أو تقسيمه على عدة عهد';
        }
        
        if (!$rules['budget_availability']) {
            $recommendations[] = 'مراجعة الميزانية المتاحة أو إعادة توزيع المخصصات';
        }

        return $recommendations;
    }
}
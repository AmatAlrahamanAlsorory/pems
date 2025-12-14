<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Expense;
use App\Models\BudgetAllocation;
use Carbon\Carbon;

class PredictiveAnalyticsService
{
    /**
     * توقع المصروفات للأسابيع القادمة
     */
    public function predictWeeklyExpenses($projectId, $weeksAhead = 4)
    {
        $project = Project::findOrFail($projectId);
        
        // جمع البيانات التاريخية
        $historicalData = $this->getHistoricalWeeklyData($projectId);
        
        if (count($historicalData) < 3) {
            return ['error' => 'بيانات غير كافية للتنبؤ'];
        }
        
        $predictions = [];
        
        for ($week = 1; $week <= $weeksAhead; $week++) {
            $prediction = $this->calculateWeeklyPrediction($historicalData, $week);
            $predictions[] = [
                'week' => $week,
                'predicted_amount' => $prediction['amount'],
                'confidence' => $prediction['confidence'],
                'date_range' => [
                    'start' => now()->addWeeks($week - 1)->startOfWeek()->format('Y-m-d'),
                    'end' => now()->addWeeks($week - 1)->endOfWeek()->format('Y-m-d')
                ]
            ];
        }
        
        return [
            'project_id' => $projectId,
            'predictions' => $predictions,
            'total_predicted' => array_sum(array_column($predictions, 'predicted_amount')),
            'budget_impact' => $this->calculateBudgetImpact($project, $predictions)
        ];
    }
    
    /**
     * توقع تجاوز الميزانية
     */
    public function predictBudgetOverrun($projectId)
    {
        $project = Project::findOrFail($projectId);
        $burnRate = $this->calculateBurnRate($projectId);
        $seasonalFactors = $this->getSeasonalFactors($projectId);
        
        $currentSpent = $project->spent_amount;
        $remainingBudget = $project->remaining_budget;
        $daysRemaining = $project->end_date ? now()->diffInDays($project->end_date) : 30;
        
        // حساب معدل الصرف المتوقع مع العوامل الموسمية
        $adjustedBurnRate = $burnRate * $seasonalFactors['current_factor'];
        $projectedSpending = $adjustedBurnRate * $daysRemaining;
        
        $overrunAmount = max(0, $projectedSpending - $remainingBudget);
        $overrunProbability = $this->calculateOverrunProbability($project, $burnRate, $seasonalFactors);
        
        return [
            'project_id' => $projectId,
            'current_spent' => $currentSpent,
            'remaining_budget' => $remainingBudget,
            'daily_burn_rate' => $burnRate,
            'projected_total_spending' => $currentSpent + $projectedSpending,
            'overrun_amount' => $overrunAmount,
            'overrun_probability' => $overrunProbability,
            'risk_level' => $this->getRiskLevel($overrunProbability),
            'recommended_actions' => $this->getRecommendedActions($overrunProbability, $overrunAmount)
        ];
    }
    
    /**
     * تحليل الأنماط الموسمية
     */
    public function analyzeSeasonalPatterns($projectId)
    {
        $expenses = Expense::where('project_id', $projectId)
            ->where('status', 'approved')
            ->selectRaw('MONTH(expense_date) as month, WEEK(expense_date) as week, SUM(amount) as total')
            ->groupBy('month', 'week')
            ->get();
        
        $monthlyPatterns = [];
        $weeklyPatterns = [];
        
        foreach ($expenses as $expense) {
            $monthlyPatterns[$expense->month] = ($monthlyPatterns[$expense->month] ?? 0) + $expense->total;
            $weeklyPatterns[$expense->week] = ($weeklyPatterns[$expense->week] ?? 0) + $expense->total;
        }
        
        return [
            'monthly_patterns' => $this->normalizePatterns($monthlyPatterns),
            'weekly_patterns' => $this->normalizePatterns($weeklyPatterns),
            'peak_months' => $this->findPeaks($monthlyPatterns),
            'peak_weeks' => $this->findPeaks($weeklyPatterns)
        ];
    }
    
    /**
     * توقع احتياجات السيولة
     */
    public function predictCashFlowNeeds($projectId, $daysAhead = 30)
    {
        $project = Project::findOrFail($projectId);
        $dailyBurnRate = $this->calculateBurnRate($projectId);
        
        $cashFlowPrediction = [];
        $cumulativeSpending = $project->spent_amount;
        
        for ($day = 1; $day <= $daysAhead; $day++) {
            $date = now()->addDays($day);
            $dayOfWeek = $date->dayOfWeek;
            
            // تعديل معدل الصرف حسب يوم الأسبوع
            $adjustedRate = $dailyBurnRate * $this->getDayOfWeekFactor($dayOfWeek);
            $cumulativeSpending += $adjustedRate;
            
            $cashFlowPrediction[] = [
                'date' => $date->format('Y-m-d'),
                'daily_spending' => $adjustedRate,
                'cumulative_spending' => $cumulativeSpending,
                'remaining_budget' => $project->total_budget - $cumulativeSpending,
                'cash_need_urgency' => $this->calculateCashUrgency($project->total_budget - $cumulativeSpending)
            ];
        }
        
        return [
            'project_id' => $projectId,
            'prediction_period' => $daysAhead,
            'daily_predictions' => $cashFlowPrediction,
            'total_predicted_spending' => $cumulativeSpending - $project->spent_amount,
            'critical_dates' => $this->findCriticalCashDates($cashFlowPrediction)
        ];
    }
    
    /**
     * تحليل كفاءة الإنفاق
     */
    public function analyzeSpendingEfficiency($projectId)
    {
        $project = Project::with('budgetAllocations.category')->findOrFail($projectId);
        
        $efficiencyAnalysis = [];
        
        foreach ($project->budgetAllocations as $allocation) {
            $actualSpent = Expense::where('project_id', $projectId)
                ->where('expense_category_id', $allocation->expense_category_id)
                ->where('status', 'approved')
                ->sum('amount');
            
            $plannedSpent = $allocation->allocated_amount;
            $efficiency = $plannedSpent > 0 ? ($actualSpent / $plannedSpent) : 0;
            
            $efficiencyAnalysis[] = [
                'category' => $allocation->category->name,
                'planned' => $plannedSpent,
                'actual' => $actualSpent,
                'efficiency_ratio' => $efficiency,
                'variance' => $actualSpent - $plannedSpent,
                'efficiency_grade' => $this->getEfficiencyGrade($efficiency),
                'recommendations' => $this->getEfficiencyRecommendations($efficiency, $allocation->category->name)
            ];
        }
        
        return [
            'project_id' => $projectId,
            'overall_efficiency' => $this->calculateOverallEfficiency($efficiencyAnalysis),
            'category_analysis' => $efficiencyAnalysis,
            'best_performing_categories' => $this->getBestPerforming($efficiencyAnalysis),
            'worst_performing_categories' => $this->getWorstPerforming($efficiencyAnalysis)
        ];
    }
    
    private function getHistoricalWeeklyData($projectId)
    {
        return Expense::where('project_id', $projectId)
            ->where('status', 'approved')
            ->selectRaw('WEEK(expense_date) as week, YEAR(expense_date) as year, SUM(amount) as total')
            ->groupBy('year', 'week')
            ->orderBy('year')
            ->orderBy('week')
            ->get()
            ->map(fn($item) => $item->total)
            ->toArray();
    }
    
    private function calculateWeeklyPrediction($historicalData, $weekAhead)
    {
        // استخدام المتوسط المتحرك مع الاتجاه
        $recentData = array_slice($historicalData, -4);
        $average = array_sum($recentData) / count($recentData);
        
        // حساب الاتجاه
        $trend = 0;
        if (count($recentData) >= 2) {
            $trend = ($recentData[count($recentData) - 1] - $recentData[0]) / (count($recentData) - 1);
        }
        
        $prediction = $average + ($trend * $weekAhead);
        $confidence = min(95, max(60, 100 - (count($historicalData) < 8 ? 20 : 0) - ($weekAhead * 5)));
        
        return [
            'amount' => max(0, $prediction),
            'confidence' => $confidence
        ];
    }
    
    private function calculateBurnRate($projectId)
    {
        $expenses = Expense::where('project_id', $projectId)
            ->where('status', 'approved')
            ->where('expense_date', '>=', now()->subDays(30))
            ->sum('amount');
        
        return $expenses / 30;
    }
    
    private function getSeasonalFactors($projectId)
    {
        $currentMonth = now()->month;
        
        // عوامل موسمية افتراضية (يمكن تحسينها بناءً على البيانات التاريخية)
        $seasonalFactors = [
            1 => 1.2,  // يناير - بداية السنة
            2 => 1.0,  // فبراير
            3 => 1.1,  // مارس - رمضان عادة
            4 => 1.3,  // أبريل - رمضان وعيد الفطر
            5 => 0.9,  // مايو
            6 => 1.0,  // يونيو
            7 => 1.1,  // يوليو - الحج
            8 => 0.8,  // أغسطس - إجازات
            9 => 1.2,  // سبتمبر - عودة النشاط
            10 => 1.1, // أكتوبر
            11 => 1.0, // نوفمبر
            12 => 1.3  // ديسمبر - نهاية السنة
        ];
        
        return [
            'current_factor' => $seasonalFactors[$currentMonth],
            'all_factors' => $seasonalFactors
        ];
    }
    
    private function calculateOverrunProbability($project, $burnRate, $seasonalFactors)
    {
        $remainingDays = $project->end_date ? now()->diffInDays($project->end_date) : 30;
        $projectedSpending = $burnRate * $remainingDays * $seasonalFactors['current_factor'];
        
        if ($projectedSpending <= $project->remaining_budget) {
            return min(20, ($projectedSpending / $project->remaining_budget) * 100);
        }
        
        $overrunRatio = $projectedSpending / $project->remaining_budget;
        return min(95, 50 + ($overrunRatio - 1) * 30);
    }
    
    private function getRiskLevel($probability)
    {
        if ($probability >= 80) return 'عالي جداً';
        if ($probability >= 60) return 'عالي';
        if ($probability >= 40) return 'متوسط';
        if ($probability >= 20) return 'منخفض';
        return 'منخفض جداً';
    }
    
    private function getRecommendedActions($probability, $overrunAmount)
    {
        $actions = [];
        
        if ($probability >= 80) {
            $actions[] = 'إيقاف المصروفات غير الضرورية فوراً';
            $actions[] = 'مراجعة عاجلة للميزانية مع الإدارة العليا';
            $actions[] = 'تقليل حجم الإنتاج أو تأجيل بعض المشاهد';
        } elseif ($probability >= 60) {
            $actions[] = 'تقليل المصروفات الاختيارية';
            $actions[] = 'مراجعة أسبوعية للميزانية';
            $actions[] = 'البحث عن مصادر تمويل إضافية';
        } elseif ($probability >= 40) {
            $actions[] = 'مراقبة دقيقة للمصروفات';
            $actions[] = 'تحسين كفاءة الإنفاق';
        }
        
        return $actions;
    }
    
    private function normalizePatterns($patterns)
    {
        if (empty($patterns)) return [];
        
        $max = max($patterns);
        return array_map(fn($value) => $value / $max, $patterns);
    }
    
    private function findPeaks($patterns)
    {
        if (empty($patterns)) return [];
        
        $average = array_sum($patterns) / count($patterns);
        return array_filter($patterns, fn($value) => $value > $average * 1.2);
    }
    
    private function getDayOfWeekFactor($dayOfWeek)
    {
        // 0 = الأحد، 6 = السبت
        $factors = [
            0 => 1.2, // الأحد - بداية الأسبوع
            1 => 1.3, // الاثنين - ذروة النشاط
            2 => 1.2, // الثلاثاء
            3 => 1.1, // الأربعاء
            4 => 1.0, // الخميس
            5 => 0.7, // الجمعة - إجازة
            6 => 0.5  // السبت - إجازة
        ];
        
        return $factors[$dayOfWeek] ?? 1.0;
    }
    
    private function calculateCashUrgency($remainingBudget)
    {
        if ($remainingBudget <= 0) return 'حرج';
        if ($remainingBudget <= 100000) return 'عاجل';
        if ($remainingBudget <= 500000) return 'مهم';
        return 'عادي';
    }
    
    private function findCriticalCashDates($predictions)
    {
        return array_filter($predictions, fn($day) => $day['cash_need_urgency'] === 'حرج' || $day['cash_need_urgency'] === 'عاجل');
    }
    
    private function getEfficiencyGrade($ratio)
    {
        if ($ratio <= 0.8) return 'ممتاز';
        if ($ratio <= 0.95) return 'جيد جداً';
        if ($ratio <= 1.05) return 'جيد';
        if ($ratio <= 1.2) return 'مقبول';
        return 'ضعيف';
    }
    
    private function getEfficiencyRecommendations($ratio, $categoryName)
    {
        if ($ratio > 1.2) {
            return ["تقليل مصروفات {$categoryName} بنسبة " . round(($ratio - 1) * 100) . "%"];
        }
        if ($ratio < 0.8) {
            return ["يمكن زيادة الاستثمار في {$categoryName} لتحسين الجودة"];
        }
        return ["الإنفاق في {$categoryName} ضمن الحدود المثلى"];
    }
    
    private function calculateOverallEfficiency($analysis)
    {
        $totalPlanned = array_sum(array_column($analysis, 'planned'));
        $totalActual = array_sum(array_column($analysis, 'actual'));
        
        return $totalPlanned > 0 ? $totalActual / $totalPlanned : 0;
    }
    
    private function getBestPerforming($analysis)
    {
        return array_filter($analysis, fn($item) => $item['efficiency_ratio'] <= 0.95);
    }
    
    private function getWorstPerforming($analysis)
    {
        return array_filter($analysis, fn($item) => $item['efficiency_ratio'] > 1.2);
    }
}
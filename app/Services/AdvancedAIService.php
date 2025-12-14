<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Expense;
use Illuminate\Support\Facades\Http;

class AdvancedAIService
{
    public function generateSmartInsights($projectId)
    {
        $project = Project::with('expenses.category')->findOrFail($projectId);
        $expenses = $project->expenses()->where('status', 'approved')->get();
        
        $insights = [
            'spending_patterns' => $this->analyzeSpendingPatterns($expenses),
            'anomaly_detection' => $this->detectAnomalies($expenses),
            'budget_optimization' => $this->suggestBudgetOptimization($project),
            'risk_assessment' => $this->assessProjectRisks($project),
            'smart_recommendations' => $this->generateSmartRecommendations($project)
        ];
        
        return $insights;
    }
    
    public function predictWithML($projectId, $daysAhead = 30)
    {
        $historicalData = $this->prepareMLData($projectId);
        
        if (count($historicalData) < 10) {
            return $this->fallbackPrediction($projectId, $daysAhead);
        }
        
        // استخدام نموذج الانحدار الخطي المتعدد
        $prediction = $this->linearRegressionPredict($historicalData, $daysAhead);
        
        // تطبيق خوارزمية ARIMA للتنبؤ بالسلاسل الزمنية
        $arimaResult = $this->arimaPredict($historicalData, $daysAhead);
        
        // دمج النتائج مع الأوزان
        $finalPrediction = $this->ensemblePrediction([$prediction, $arimaResult]);
        
        return [
            'predictions' => $finalPrediction,
            'confidence_interval' => $this->calculateConfidenceInterval($finalPrediction),
            'model_accuracy' => $this->calculateModelAccuracy($historicalData),
            'feature_importance' => $this->getFeatureImportance($historicalData)
        ];
    }
    
    public function detectFraudWithAI($expenses)
    {
        $suspiciousExpenses = [];
        
        foreach ($expenses as $expense) {
            $riskScore = $this->calculateFraudRiskScore($expense);
            
            if ($riskScore > 0.7) {
                $suspiciousExpenses[] = [
                    'expense' => $expense,
                    'risk_score' => $riskScore,
                    'risk_factors' => $this->identifyRiskFactors($expense),
                    'recommended_action' => $this->getRecommendedAction($riskScore)
                ];
            }
        }
        
        return $suspiciousExpenses;
    }
    
    public function optimizeBudgetAllocation($projectId)
    {
        $project = Project::with('budgetAllocations.category')->findOrFail($projectId);
        $historicalPerformance = $this->getHistoricalPerformance($projectId);
        
        $optimizedAllocation = [];
        
        foreach ($project->budgetAllocations as $allocation) {
            $categoryPerformance = $historicalPerformance[$allocation->expense_category_id] ?? [];
            
            $optimizedAmount = $this->calculateOptimalAllocation(
                $allocation->allocated_amount,
                $categoryPerformance,
                $project->total_budget
            );
            
            $optimizedAllocation[] = [
                'category' => $allocation->category->name,
                'current_allocation' => $allocation->allocated_amount,
                'optimized_allocation' => $optimizedAmount,
                'improvement_potential' => $optimizedAmount - $allocation->allocated_amount,
                'confidence' => $this->calculateOptimizationConfidence($categoryPerformance)
            ];
        }
        
        return $optimizedAllocation;
    }
    
    private function analyzeSpendingPatterns($expenses)
    {
        $patterns = [];
        
        // تحليل الأنماط اليومية
        $dailyPattern = $expenses->groupBy(function($expense) {
            return $expense->expense_date->dayOfWeek;
        })->map(function($group) {
            return $group->sum('amount');
        });
        
        // تحليل الأنماط الشهرية
        $monthlyPattern = $expenses->groupBy(function($expense) {
            return $expense->expense_date->month;
        })->map(function($group) {
            return $group->sum('amount');
        });
        
        // اكتشاف الاتجاهات
        $trend = $this->calculateTrend($expenses);
        
        return [
            'daily_patterns' => $dailyPattern,
            'monthly_patterns' => $monthlyPattern,
            'trend_analysis' => $trend,
            'seasonality' => $this->detectSeasonality($expenses),
            'peak_spending_times' => $this->identifyPeakTimes($expenses)
        ];
    }
    
    private function detectAnomalies($expenses)
    {
        $anomalies = [];
        $amounts = $expenses->pluck('amount')->toArray();
        
        if (count($amounts) < 5) return $anomalies;
        
        $mean = array_sum($amounts) / count($amounts);
        $stdDev = $this->calculateStandardDeviation($amounts, $mean);
        
        foreach ($expenses as $expense) {
            $zScore = abs(($expense->amount - $mean) / $stdDev);
            
            if ($zScore > 2.5) { // قيم شاذة
                $anomalies[] = [
                    'expense_id' => $expense->id,
                    'amount' => $expense->amount,
                    'z_score' => $zScore,
                    'anomaly_type' => $zScore > 3 ? 'extreme' : 'moderate',
                    'explanation' => $this->explainAnomaly($expense, $mean, $stdDev)
                ];
            }
        }
        
        return $anomalies;
    }
    
    private function suggestBudgetOptimization($project)
    {
        $suggestions = [];
        
        foreach ($project->budgetAllocations as $allocation) {
            $utilization = $allocation->spent_amount / $allocation->allocated_amount;
            
            if ($utilization < 0.5) {
                $suggestions[] = [
                    'category' => $allocation->category->name,
                    'type' => 'underutilized',
                    'current_utilization' => $utilization * 100,
                    'suggestion' => 'يمكن تقليل الميزانية المخصصة لهذه الفئة',
                    'potential_savings' => $allocation->allocated_amount * (0.5 - $utilization)
                ];
            } elseif ($utilization > 0.9) {
                $suggestions[] = [
                    'category' => $allocation->category->name,
                    'type' => 'overutilized',
                    'current_utilization' => $utilization * 100,
                    'suggestion' => 'قد تحتاج هذه الفئة لميزانية إضافية',
                    'recommended_increase' => $allocation->allocated_amount * 0.2
                ];
            }
        }
        
        return $suggestions;
    }
    
    private function assessProjectRisks($project)
    {
        $risks = [];
        
        // تقييم مخاطر الميزانية
        $budgetRisk = $project->budget_percentage;
        if ($budgetRisk > 80) {
            $risks[] = [
                'type' => 'budget_overrun',
                'severity' => $budgetRisk > 95 ? 'high' : 'medium',
                'probability' => min(95, ($budgetRisk - 50) * 2),
                'impact' => 'تجاوز الميزانية المخططة',
                'mitigation' => 'تقليل المصروفات غير الضرورية'
            ];
        }
        
        // تقييم مخاطر الجدولة
        if ($project->end_date && now()->diffInDays($project->end_date) < 30) {
            $risks[] = [
                'type' => 'schedule_risk',
                'severity' => 'medium',
                'probability' => 70,
                'impact' => 'ضغط زمني قد يؤدي لزيادة التكاليف',
                'mitigation' => 'مراجعة الجدولة وتحديد الأولويات'
            ];
        }
        
        return $risks;
    }
    
    private function generateSmartRecommendations($project)
    {
        $recommendations = [];
        
        // توصيات بناءً على الأداء التاريخي
        $historicalData = $this->getHistoricalPerformance($project->id);
        
        // توصيات التوفير
        $savingOpportunities = $this->identifySavingOpportunities($project);
        
        // توصيات التحسين
        $improvementAreas = $this->identifyImprovementAreas($project);
        
        return array_merge($savingOpportunities, $improvementAreas);
    }
    
    private function prepareMLData($projectId)
    {
        $expenses = Expense::where('project_id', $projectId)
            ->where('status', 'approved')
            ->orderBy('expense_date')
            ->get();
        
        $data = [];
        foreach ($expenses as $expense) {
            $data[] = [
                'date' => $expense->expense_date->timestamp,
                'amount' => $expense->amount,
                'category_id' => $expense->expense_category_id,
                'day_of_week' => $expense->expense_date->dayOfWeek,
                'month' => $expense->expense_date->month,
                'cumulative' => $expenses->where('expense_date', '<=', $expense->expense_date)->sum('amount')
            ];
        }
        
        return $data;
    }
    
    private function linearRegressionPredict($data, $daysAhead)
    {
        // تطبيق الانحدار الخطي البسيط
        $n = count($data);
        $sumX = array_sum(array_column($data, 'date'));
        $sumY = array_sum(array_column($data, 'amount'));
        $sumXY = 0;
        $sumX2 = 0;
        
        foreach ($data as $point) {
            $sumXY += $point['date'] * $point['amount'];
            $sumX2 += $point['date'] * $point['date'];
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;
        
        $predictions = [];
        $lastDate = max(array_column($data, 'date'));
        
        for ($i = 1; $i <= $daysAhead; $i++) {
            $futureDate = $lastDate + ($i * 86400); // إضافة يوم
            $prediction = $slope * $futureDate + $intercept;
            $predictions[] = max(0, $prediction); // لا يمكن أن تكون المصروفات سالبة
        }
        
        return $predictions;
    }
    
    private function arimaPredict($data, $daysAhead)
    {
        // تطبيق مبسط لنموذج ARIMA
        $values = array_column($data, 'amount');
        $n = count($values);
        
        if ($n < 3) return array_fill(0, $daysAhead, end($values));
        
        // حساب المتوسط المتحرك
        $movingAverage = [];
        for ($i = 2; $i < $n; $i++) {
            $movingAverage[] = ($values[$i-2] + $values[$i-1] + $values[$i]) / 3;
        }
        
        // التنبؤ بناءً على الاتجاه
        $trend = 0;
        if (count($movingAverage) > 1) {
            $trend = (end($movingAverage) - $movingAverage[0]) / count($movingAverage);
        }
        
        $predictions = [];
        $lastValue = end($movingAverage) ?: end($values);
        
        for ($i = 1; $i <= $daysAhead; $i++) {
            $prediction = $lastValue + ($trend * $i);
            $predictions[] = max(0, $prediction);
        }
        
        return $predictions;
    }
    
    private function ensemblePrediction($predictions)
    {
        $ensemble = [];
        $numModels = count($predictions);
        $numPredictions = count($predictions[0]);
        
        for ($i = 0; $i < $numPredictions; $i++) {
            $sum = 0;
            foreach ($predictions as $modelPredictions) {
                $sum += $modelPredictions[$i];
            }
            $ensemble[] = $sum / $numModels;
        }
        
        return $ensemble;
    }
    
    private function calculateConfidenceInterval($predictions)
    {
        $mean = array_sum($predictions) / count($predictions);
        $variance = 0;
        
        foreach ($predictions as $value) {
            $variance += pow($value - $mean, 2);
        }
        $variance /= count($predictions);
        $stdDev = sqrt($variance);
        
        return [
            'lower_bound' => array_map(fn($p) => max(0, $p - 1.96 * $stdDev), $predictions),
            'upper_bound' => array_map(fn($p) => $p + 1.96 * $stdDev, $predictions)
        ];
    }
    
    private function calculateStandardDeviation($values, $mean)
    {
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        return sqrt($variance / count($values));
    }
    
    private function calculateFraudRiskScore($expense)
    {
        $score = 0;
        
        // فحص المبلغ غير المعتاد
        $categoryAverage = Expense::where('expense_category_id', $expense->expense_category_id)
            ->where('project_id', $expense->project_id)
            ->avg('amount');
        
        if ($expense->amount > $categoryAverage * 3) {
            $score += 0.3;
        }
        
        // فحص التوقيت المشبوه
        if ($expense->expense_date->isWeekend()) {
            $score += 0.2;
        }
        
        // فحص الفواتير المكررة
        $duplicates = Expense::where('invoice_number', $expense->invoice_number)
            ->where('amount', $expense->amount)
            ->where('id', '!=', $expense->id)
            ->count();
        
        if ($duplicates > 0) {
            $score += 0.5;
        }
        
        return min(1, $score);
    }
    
    private function identifyRiskFactors($expense)
    {
        $factors = [];
        
        if ($expense->amount > 10000) {
            $factors[] = 'مبلغ كبير';
        }
        
        if (!$expense->invoice_file) {
            $factors[] = 'لا توجد فاتورة';
        }
        
        if ($expense->expense_date->isWeekend()) {
            $factors[] = 'تم في عطلة نهاية الأسبوع';
        }
        
        return $factors;
    }
    
    private function getRecommendedAction($riskScore)
    {
        if ($riskScore > 0.8) {
            return 'مراجعة فورية مطلوبة';
        } elseif ($riskScore > 0.6) {
            return 'مراجعة إضافية موصى بها';
        } else {
            return 'مراقبة عادية';
        }
    }
}
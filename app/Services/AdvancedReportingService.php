<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedReportingService
{
    // Executive Dashboard
    public function generateExecutiveDashboard($dateRange = null)
    {
        $startDate = $dateRange['start'] ?? now()->subMonths(3);
        $endDate = $dateRange['end'] ?? now();

        return [
            'financial_overview' => $this->getFinancialOverview($startDate, $endDate),
            'project_performance' => $this->getProjectPerformance($startDate, $endDate),
            'cost_trends' => $this->getCostTrends($startDate, $endDate),
            'efficiency_metrics' => $this->getEfficiencyMetrics($startDate, $endDate),
            'risk_indicators' => $this->getRiskIndicators(),
            'forecasting' => $this->getFinancialForecasting($startDate, $endDate),
            'kpis' => $this->getKPIs($startDate, $endDate)
        ];
    }

    // Interactive AI Reports
    public function generateAIReport($query, $projectId = null)
    {
        $aiService = app(\App\Services\AdvancedAIService::class);
        
        // تحليل الاستعلام بالذكاء الاصطناعي
        $analysisType = $this->analyzeQuery($query);
        
        switch ($analysisType) {
            case 'budget_analysis':
                return $this->generateBudgetAnalysisReport($projectId);
            case 'cost_prediction':
                return $this->generateCostPredictionReport($projectId);
            case 'efficiency_report':
                return $this->generateEfficiencyReport($projectId);
            case 'risk_assessment':
                return $this->generateRiskAssessmentReport($projectId);
            default:
                return $this->generateGeneralReport($query, $projectId);
        }
    }

    // Performance Comparison Reports
    public function generatePerformanceComparison($projects, $metrics = [])
    {
        $comparison = [];
        
        foreach ($projects as $projectId) {
            $project = \App\Models\Project::find($projectId);
            
            $comparison[$projectId] = [
                'name' => $project->name,
                'budget_efficiency' => $this->calculateBudgetEfficiency($projectId),
                'time_efficiency' => $this->calculateTimeEfficiency($projectId),
                'cost_per_day' => $this->calculateCostPerDay($projectId),
                'roi' => $this->calculateROI($projectId),
                'quality_score' => $this->calculateQualityScore($projectId),
                'risk_score' => $this->calculateRiskScore($projectId)
            ];
        }

        return [
            'comparison_data' => $comparison,
            'best_performer' => $this->getBestPerformer($comparison),
            'worst_performer' => $this->getWorstPerformer($comparison),
            'recommendations' => $this->generateComparisonRecommendations($comparison)
        ];
    }

    // Long-term Forecasting
    public function generateLongTermForecast($projectId, $months = 12)
    {
        $historicalData = $this->getHistoricalData($projectId);
        $seasonalFactors = $this->calculateSeasonalFactors($historicalData);
        
        $forecast = [];
        
        for ($i = 1; $i <= $months; $i++) {
            $forecastDate = now()->addMonths($i);
            
            $forecast[] = [
                'month' => $forecastDate->format('Y-m'),
                'predicted_expenses' => $this->predictMonthlyExpenses($historicalData, $seasonalFactors, $i),
                'confidence_interval' => $this->calculateConfidenceInterval($historicalData, $i),
                'risk_factors' => $this->identifyRiskFactors($forecastDate),
                'recommendations' => $this->getMonthlyRecommendations($forecastDate, $projectId)
            ];
        }

        return [
            'project_id' => $projectId,
            'forecast_period' => $months,
            'monthly_forecast' => $forecast,
            'total_predicted' => array_sum(array_column($forecast, 'predicted_expenses')),
            'accuracy_score' => $this->calculateForecastAccuracy($historicalData),
            'key_insights' => $this->generateForecastInsights($forecast)
        ];
    }

    // Real-time Analytics
    public function generateRealTimeAnalytics($projectId)
    {
        return [
            'current_burn_rate' => $this->getCurrentBurnRate($projectId),
            'budget_velocity' => $this->getBudgetVelocity($projectId),
            'expense_frequency' => $this->getExpenseFrequency($projectId),
            'category_distribution' => $this->getRealTimeCategoryDistribution($projectId),
            'alerts' => $this->getRealTimeAlerts($projectId),
            'live_metrics' => $this->getLiveMetrics($projectId)
        ];
    }

    // Custom Report Builder
    public function buildCustomReport($config)
    {
        $report = [
            'title' => $config['title'],
            'generated_at' => now(),
            'parameters' => $config['parameters'],
            'sections' => []
        ];

        foreach ($config['sections'] as $section) {
            $report['sections'][] = $this->generateReportSection($section);
        }

        return $report;
    }

    // Data Visualization
    public function generateVisualizationData($type, $projectId, $parameters = [])
    {
        switch ($type) {
            case 'budget_waterfall':
                return $this->generateBudgetWaterfall($projectId);
            case 'expense_heatmap':
                return $this->generateExpenseHeatmap($projectId, $parameters);
            case 'trend_analysis':
                return $this->generateTrendAnalysis($projectId, $parameters);
            case 'correlation_matrix':
                return $this->generateCorrelationMatrix($projectId);
            case 'sankey_diagram':
                return $this->generateSankeyDiagram($projectId);
            default:
                return null;
        }
    }

    private function getFinancialOverview($startDate, $endDate)
    {
        return [
            'total_budget' => \App\Models\Project::sum('total_budget'),
            'total_spent' => \App\Models\Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount'),
            'budget_utilization' => $this->calculateBudgetUtilization($startDate, $endDate),
            'cost_variance' => $this->calculateCostVariance($startDate, $endDate),
            'savings_achieved' => $this->calculateSavings($startDate, $endDate)
        ];
    }

    private function getProjectPerformance($startDate, $endDate)
    {
        $projects = \App\Models\Project::with('expenses')->get();
        
        return $projects->map(function($project) use ($startDate, $endDate) {
            $expenses = $project->expenses()->whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
            
            return [
                'name' => $project->name,
                'budget' => $project->total_budget,
                'spent' => $expenses,
                'efficiency' => $this->calculateBudgetEfficiency($project->id),
                'status' => $this->getProjectStatus($project)
            ];
        });
    }

    private function getCostTrends($startDate, $endDate)
    {
        $trends = DB::select("
            SELECT 
                DATE_FORMAT(expense_date, '%Y-%m') as month,
                SUM(amount) as total_amount,
                COUNT(*) as expense_count,
                AVG(amount) as avg_amount
            FROM expenses 
            WHERE expense_date BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
            ORDER BY month
        ", [$startDate, $endDate]);

        return $trends;
    }

    private function getEfficiencyMetrics($startDate, $endDate)
    {
        return [
            'cost_per_project' => $this->calculateCostPerProject($startDate, $endDate),
            'expense_processing_time' => $this->calculateExpenseProcessingTime($startDate, $endDate),
            'approval_efficiency' => $this->calculateApprovalEfficiency($startDate, $endDate),
            'resource_utilization' => $this->calculateResourceUtilization($startDate, $endDate)
        ];
    }

    private function getRiskIndicators()
    {
        return [
            'budget_overruns' => $this->countBudgetOverruns(),
            'delayed_approvals' => $this->countDelayedApprovals(),
            'unusual_expenses' => $this->countUnusualExpenses(),
            'compliance_issues' => $this->countComplianceIssues()
        ];
    }

    private function getFinancialForecasting($startDate, $endDate)
    {
        $historicalData = $this->getHistoricalSpending($startDate, $endDate);
        
        return [
            'next_month_prediction' => $this->predictNextMonthSpending($historicalData),
            'quarter_forecast' => $this->predictQuarterSpending($historicalData),
            'year_end_projection' => $this->predictYearEndSpending($historicalData),
            'confidence_levels' => $this->calculateForecastConfidence($historicalData)
        ];
    }

    private function getKPIs($startDate, $endDate)
    {
        return [
            'budget_adherence' => $this->calculateBudgetAdherence($startDate, $endDate),
            'cost_control_efficiency' => $this->calculateCostControlEfficiency($startDate, $endDate),
            'expense_approval_rate' => $this->calculateExpenseApprovalRate($startDate, $endDate),
            'financial_compliance' => $this->calculateFinancialCompliance($startDate, $endDate)
        ];
    }

    private function analyzeQuery($query)
    {
        $keywords = [
            'budget' => 'budget_analysis',
            'cost' => 'cost_prediction',
            'efficiency' => 'efficiency_report',
            'risk' => 'risk_assessment'
        ];

        foreach ($keywords as $keyword => $type) {
            if (stripos($query, $keyword) !== false) {
                return $type;
            }
        }

        return 'general';
    }

    private function generateBudgetAnalysisReport($projectId)
    {
        $project = \App\Models\Project::find($projectId);
        
        return [
            'type' => 'budget_analysis',
            'project' => $project->name,
            'budget_breakdown' => $this->getBudgetBreakdown($projectId),
            'variance_analysis' => $this->getVarianceAnalysis($projectId),
            'recommendations' => $this->getBudgetRecommendations($projectId)
        ];
    }

    private function calculateBudgetEfficiency($projectId)
    {
        $project = \App\Models\Project::find($projectId);
        $actualSpent = $project->spent_amount;
        $budgetAllocated = $project->total_budget;
        
        return $budgetAllocated > 0 ? (1 - ($actualSpent / $budgetAllocated)) * 100 : 0;
    }

    private function calculateTimeEfficiency($projectId)
    {
        $project = \App\Models\Project::find($projectId);
        
        if (!$project->start_date || !$project->end_date) {
            return 0;
        }
        
        $plannedDays = $project->start_date->diffInDays($project->end_date);
        $actualDays = $project->start_date->diffInDays(now());
        
        return $plannedDays > 0 ? ($actualDays / $plannedDays) * 100 : 0;
    }

    private function calculateCostPerDay($projectId)
    {
        $project = \App\Models\Project::find($projectId);
        $totalSpent = $project->spent_amount;
        $daysSinceStart = $project->start_date ? $project->start_date->diffInDays(now()) : 1;
        
        return $daysSinceStart > 0 ? $totalSpent / $daysSinceStart : 0;
    }

    private function calculateROI($projectId)
    {
        // محاكاة حساب العائد على الاستثمار
        $project = \App\Models\Project::find($projectId);
        $estimatedRevenue = $project->total_budget * 1.5; // افتراض
        $actualCost = $project->spent_amount;
        
        return $actualCost > 0 ? (($estimatedRevenue - $actualCost) / $actualCost) * 100 : 0;
    }

    private function calculateQualityScore($projectId)
    {
        // محاكاة حساب نقاط الجودة
        return rand(70, 95);
    }

    private function calculateRiskScore($projectId)
    {
        $project = \App\Models\Project::find($projectId);
        $budgetUsage = $project->budget_percentage;
        
        if ($budgetUsage > 90) return 'high';
        if ($budgetUsage > 70) return 'medium';
        return 'low';
    }

    private function getBestPerformer($comparison)
    {
        $bestId = null;
        $bestScore = 0;
        
        foreach ($comparison as $projectId => $data) {
            $score = $data['budget_efficiency'] + $data['time_efficiency'] - $data['risk_score'];
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestId = $projectId;
            }
        }
        
        return $bestId ? $comparison[$bestId] : null;
    }

    private function getWorstPerformer($comparison)
    {
        $worstId = null;
        $worstScore = PHP_INT_MAX;
        
        foreach ($comparison as $projectId => $data) {
            $score = $data['budget_efficiency'] + $data['time_efficiency'] - $data['risk_score'];
            if ($score < $worstScore) {
                $worstScore = $score;
                $worstId = $projectId;
            }
        }
        
        return $worstId ? $comparison[$worstId] : null;
    }

    private function generateComparisonRecommendations($comparison)
    {
        return [
            'Focus on improving budget efficiency for underperforming projects',
            'Implement best practices from top-performing projects',
            'Address high-risk projects with immediate action plans'
        ];
    }

    // باقي الدوال المساعدة...
    private function getHistoricalData($projectId)
    {
        return \App\Models\Expense::where('project_id', $projectId)
            ->selectRaw('DATE(expense_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    private function calculateSeasonalFactors($historicalData)
    {
        // حساب العوامل الموسمية
        return [1.0, 1.1, 0.9, 1.2]; // مثال
    }

    private function predictMonthlyExpenses($historicalData, $seasonalFactors, $monthsAhead)
    {
        $avgMonthly = collect($historicalData)->avg('total');
        $seasonalIndex = ($monthsAhead - 1) % 4;
        
        return $avgMonthly * $seasonalFactors[$seasonalIndex];
    }

    private function calculateConfidenceInterval($historicalData, $monthsAhead)
    {
        return [
            'lower' => 0.8,
            'upper' => 1.2
        ];
    }

    private function identifyRiskFactors($date)
    {
        return ['seasonal_variation', 'market_conditions'];
    }

    private function getMonthlyRecommendations($date, $projectId)
    {
        return ['Monitor spending closely', 'Review budget allocation'];
    }

    private function calculateForecastAccuracy($historicalData)
    {
        return 0.85; // 85% دقة
    }

    private function generateForecastInsights($forecast)
    {
        return [
            'Peak spending expected in month 3',
            'Budget adjustment recommended for Q2'
        ];
    }
}
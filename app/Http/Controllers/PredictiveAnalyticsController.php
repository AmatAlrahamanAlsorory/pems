<?php

namespace App\Http\Controllers;

use App\Services\PredictiveAnalyticsService;
use Illuminate\Http\Request;

class PredictiveAnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(PredictiveAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function weeklyPrediction(Request $request, $projectId)
    {
        $weeksAhead = $request->get('weeks', 4);
        $prediction = $this->analyticsService->predictWeeklyExpenses($projectId, $weeksAhead);
        
        return response()->json($prediction);
    }

    public function budgetOverrunPrediction($projectId)
    {
        $prediction = $this->analyticsService->predictBudgetOverrun($projectId);
        
        return response()->json($prediction);
    }

    public function seasonalAnalysis($projectId)
    {
        $analysis = $this->analyticsService->analyzeSeasonalPatterns($projectId);
        
        return response()->json($analysis);
    }

    public function cashFlowPrediction(Request $request, $projectId)
    {
        $daysAhead = $request->get('days', 30);
        $prediction = $this->analyticsService->predictCashFlowNeeds($projectId, $daysAhead);
        
        return response()->json($prediction);
    }

    public function spendingEfficiency($projectId)
    {
        $analysis = $this->analyticsService->analyzeSpendingEfficiency($projectId);
        
        return response()->json($analysis);
    }

    public function dashboard($projectId)
    {
        $data = [
            'budget_prediction' => $this->analyticsService->predictBudgetOverrun($projectId),
            'weekly_prediction' => $this->analyticsService->predictWeeklyExpenses($projectId, 4),
            'efficiency_analysis' => $this->analyticsService->analyzeSpendingEfficiency($projectId),
            'cash_flow' => $this->analyticsService->predictCashFlowNeeds($projectId, 14)
        ];
        
        return response()->json($data);
    }
}
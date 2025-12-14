<?php

namespace App\Http\Controllers;

use App\Services\AIAssistantService;
use App\Services\PredictiveAnalyticsService;
use App\Services\FraudDetectionService;
use App\Models\Project;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(
        private AIAssistantService $assistant,
        private PredictiveAnalyticsService $analytics,
        private FraudDetectionService $fraud
    ) {}
    
    public function assistant(Request $request)
    {
        $request->validate(['query' => 'required|string|max:500']);
        
        $response = $this->assistant->processQuery($request->query, auth()->user());
        
        return response()->json($response);
    }
    
    public function predictBudget($projectId)
    {
        $project = Project::findOrFail($projectId);
        $prediction = $this->analytics->predictProjectBudgetOverrun($project);
        
        return response()->json($prediction);
    }
    
    public function projectInsights($projectId)
    {
        $project = Project::findOrFail($projectId);
        $insights = $this->analytics->generateProjectInsights($project);
        
        return response()->json($insights);
    }
    
    public function fraudReport(Request $request)
    {
        $report = $this->fraud->generateFraudReport(
            $request->date_from,
            $request->date_to
        );
        
        return response()->json($report);
    }
    
    public function autoReport(Request $request)
    {
        $type = $request->input('type', 'daily');
        $report = $this->assistant->generateAutoReport($type);
        
        return response()->json($report);
    }
}

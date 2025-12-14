<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Advanced Custody Management API
Route::prefix('custody-advanced')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/validate-request', [\App\Http\Controllers\API\AdvancedCustodyController::class, 'validateCustodyRequest']);
    Route::post('/smart-approval', [\App\Http\Controllers\API\AdvancedCustodyController::class, 'smartApproval']);
    Route::get('/performance-analysis/{userId}', [\App\Http\Controllers\API\AdvancedCustodyController::class, 'getUserPerformanceAnalysis']);
    Route::post('/risk-assessment', [\App\Http\Controllers\API\AdvancedCustodyController::class, 'assessRisk']);
    Route::get('/rules/active', [\App\Http\Controllers\API\AdvancedCustodyController::class, 'getActiveRules']);
});

// Dynamic Budget Management API
Route::prefix('budget-dynamic')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/create-smart-periods', [\App\Http\Controllers\API\DynamicBudgetController::class, 'createSmartPeriodBudgets']);
    Route::post('/intelligent-adjustments/{projectId}', [\App\Http\Controllers\API\DynamicBudgetController::class, 'performIntelligentAdjustments']);
    Route::get('/advanced-forecasts/{projectId}', [\App\Http\Controllers\API\DynamicBudgetController::class, 'generateAdvancedForecasts']);
    Route::post('/ai-optimization/{projectId}', [\App\Http\Controllers\API\DynamicBudgetController::class, 'applyAIOptimization']);
    Route::get('/period-analysis/{projectId}', [\App\Http\Controllers\API\DynamicBudgetController::class, 'analyzePeriodPerformance']);
});

// Comprehensive Predictive Analytics API
Route::prefix('analytics-predictive')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/comprehensive/{projectId}', [\App\Http\Controllers\API\PredictiveAnalyticsController::class, 'generateComprehensiveAnalytics']);
    Route::get('/financial-predictions/{projectId}', [\App\Http\Controllers\API\PredictiveAnalyticsController::class, 'getFinancialPredictions']);
    Route::get('/risk-analytics/{projectId}', [\App\Http\Controllers\API\PredictiveAnalyticsController::class, 'getRiskAnalytics']);
    Route::get('/performance-predictions/{projectId}', [\App\Http\Controllers\API\PredictiveAnalyticsController::class, 'getPerformancePredictions']);
    Route::get('/market-intelligence/{projectId}', [\App\Http\Controllers\API\PredictiveAnalyticsController::class, 'getMarketIntelligence']);
    Route::post('/ml-advanced/{projectId}', [\App\Http\Controllers\API\PredictiveAnalyticsController::class, 'applyAdvancedML']);
    Route::get('/real-time-adjustments/{projectId}', [\App\Http\Controllers\API\PredictiveAnalyticsController::class, 'getRealTimeAdjustments']);
});

// Intelligent Alerts API
Route::prefix('alerts-intelligent')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/{userId}', [\App\Http\Controllers\API\IntelligentAlertsController::class, 'getUserAlerts']);
    Route::get('/project/{projectId}', [\App\Http\Controllers\API\IntelligentAlertsController::class, 'getProjectAlerts']);
    Route::post('/predictive-setup', [\App\Http\Controllers\API\IntelligentAlertsController::class, 'setupPredictiveAlerts']);
    Route::post('/mark-read/{alertId}', [\App\Http\Controllers\API\IntelligentAlertsController::class, 'markAsRead']);
    Route::post('/resolve/{alertId}', [\App\Http\Controllers\API\IntelligentAlertsController::class, 'resolveAlert']);
});

// Performance Optimization API
Route::prefix('performance-optimization')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/project/{projectId}', [\App\Http\Controllers\API\PerformanceOptimizationController::class, 'getOptimizations']);
    Route::post('/analyze/{projectId}', [\App\Http\Controllers\API\PerformanceOptimizationController::class, 'analyzePerformance']);
    Route::post('/implement/{optimizationId}', [\App\Http\Controllers\API\PerformanceOptimizationController::class, 'implementOptimization']);
    Route::get('/roi-analysis/{optimizationId}', [\App\Http\Controllers\API\PerformanceOptimizationController::class, 'getROIAnalysis']);
});

// Advanced ML Models API
Route::prefix('ml-models')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [\App\Http\Controllers\API\MLModelsController::class, 'index']);
    Route::post('/train', [\App\Http\Controllers\API\MLModelsController::class, 'trainModel']);
    Route::post('/predict', [\App\Http\Controllers\API\MLModelsController::class, 'makePrediction']);
    Route::get('/performance/{modelId}', [\App\Http\Controllers\API\MLModelsController::class, 'getModelPerformance']);
    Route::post('/retrain/{modelId}', [\App\Http\Controllers\API\MLModelsController::class, 'retrainModel']);
});

// Real-time Dashboard API
Route::prefix('dashboard-realtime')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/metrics/{projectId}', [\App\Http\Controllers\API\RealTimeDashboardController::class, 'getRealTimeMetrics']);
    Route::get('/predictive/{projectId}', [\App\Http\Controllers\API\RealTimeDashboardController::class, 'getPredictiveDashboard']);
    Route::get('/executive-summary/{projectId}', [\App\Http\Controllers\API\RealTimeDashboardController::class, 'getExecutiveSummary']);
    Route::post('/custom-dashboard', [\App\Http\Controllers\API\RealTimeDashboardController::class, 'createCustomDashboard']);
    Route::get('/ai-recommendations/{projectId}', [\App\Http\Controllers\API\RealTimeDashboardController::class, 'getAIRecommendations']);
});

// Advanced Analytics API
Route::prefix('analytics-advanced')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/comprehensive-forecast/{projectId}', [\App\Http\Controllers\API\AdvancedAnalyticsController::class, 'getComprehensiveForecast']);
    Route::post('/scenario-modeling', [\App\Http\Controllers\API\AdvancedAnalyticsController::class, 'performScenarioModeling']);
    Route::get('/risk-heatmap/{projectId}', [\App\Http\Controllers\API\AdvancedAnalyticsController::class, 'getRiskHeatmap']);
    Route::post('/what-if-analysis', [\App\Http\Controllers\API\AdvancedAnalyticsController::class, 'performWhatIfAnalysis']);
    Route::get('/benchmarking-report/{projectId}', [\App\Http\Controllers\API\AdvancedAnalyticsController::class, 'getBenchmarkingReport']);
});

// Automated Adjustments API
Route::prefix('auto-adjustments')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/project/{projectId}', [\App\Http\Controllers\API\AutoAdjustmentsController::class, 'getProjectAdjustments']);
    Route::post('/trigger/{projectId}', [\App\Http\Controllers\API\AutoAdjustmentsController::class, 'triggerAdjustments']);
    Route::post('/approve/{adjustmentId}', [\App\Http\Controllers\API\AutoAdjustmentsController::class, 'approveAdjustment']);
    Route::get('/history/{projectId}', [\App\Http\Controllers\API\AutoAdjustmentsController::class, 'getAdjustmentHistory']);
});

// Market Intelligence API
Route::prefix('market-intelligence')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/industry-benchmarks', [\App\Http\Controllers\API\MarketIntelligenceController::class, 'getIndustryBenchmarks']);
    Route::get('/competitive-analysis/{projectId}', [\App\Http\Controllers\API\MarketIntelligenceController::class, 'getCompetitiveAnalysis']);
    Route::get('/trend-analysis', [\App\Http\Controllers\API\MarketIntelligenceController::class, 'getTrendAnalysis']);
    Route::post('/update-market-data', [\App\Http\Controllers\API\MarketIntelligenceController::class, 'updateMarketData']);
});
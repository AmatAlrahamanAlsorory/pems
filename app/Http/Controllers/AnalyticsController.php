<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::canViewReports(auth()->user())) {
            abort(403, 'غير مصرح لك بعرض التحليلات');
        }
        
        $projects = Project::withCount('expenses')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('analytics.index', compact('projects'));
    }

    public function predictive($projectId)
    {
        if (!PermissionHelper::canViewReports(auth()->user())) {
            abort(403, 'غير مصرح لك بعرض التحليلات');
        }
        
        $project = Project::with(['expenses' => function($query) {
            $query->where('status', 'approved')
                  ->orderBy('expense_date', 'desc');
        }])->findOrFail($projectId);
        
        // حساب البيانات الحقيقية
        $totalSpent = $project->expenses->sum('amount');
        $budgetUsed = $project->total_budget > 0 ? ($totalSpent / $project->total_budget) * 100 : 0;
        
        // المصروفات الشهرية
        $monthlyExpenses = $project->expenses
            ->groupBy(function($expense) {
                return \Carbon\Carbon::parse($expense->expense_date)->format('Y-m');
            })
            ->map(function($expenses) {
                return $expenses->sum('amount');
            });
        
        // المصروفات حسب الفئات
        $categoryExpenses = $project->expenses
            ->groupBy('expense_category_id')
            ->map(function($expenses) {
                return [
                    'category' => $expenses->first()->category->name_ar ?? 'غير محدد',
                    'amount' => $expenses->sum('amount'),
                    'count' => $expenses->count()
                ];
            })
            ->values();
        
        $analyticsData = [
            'project_stats' => [
                'total_budget' => $project->total_budget,
                'total_spent' => $totalSpent,
                'budget_used_percentage' => $budgetUsed,
                'remaining_budget' => $project->total_budget - $totalSpent,
                'expenses_count' => $project->expenses->count()
            ],
            'monthly_expenses' => $monthlyExpenses,
            'category_expenses' => $categoryExpenses,
            'recent_expenses' => $project->expenses->take(10)
        ];
        
        return view('analytics.predictive', compact('project', 'analyticsData'));
    }
}
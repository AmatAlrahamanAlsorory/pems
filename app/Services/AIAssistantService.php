<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AIAssistantService
{
    public function processQuery($query, User $user)
    {
        $query = trim($query);
        
        // محاولة استخدام OpenAI إذا كان متوفر
        if (config('services.openai.key')) {
            return $this->processWithAI($query, $user);
        }
        
        // استخدام المعالجة المحلية
        return $this->processLocally($query, $user);
    }
    
    private function processWithAI($query, User $user)
    {
        try {
            $context = $this->buildContext($user);
            
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . config('services.openai.key')])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => "أنت مساعد ذكي لنظام إدارة مصروفات الإنتاج الفني. البيانات المتاحة: {$context}"],
                        ['role' => 'user', 'content' => $query]
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.7
                ]);
            
            if ($response->successful()) {
                $answer = $response->json()['choices'][0]['message']['content'];
                return ['type' => 'ai', 'message' => $answer, 'data' => []];
            }
        } catch (\Exception $e) {
            \Log::error('AI Error: ' . $e->getMessage());
        }
        
        return $this->processLocally($query, $user);
    }
    
    private function buildContext(User $user)
    {
        $projects = Project::count();
        $budget = Project::sum('total_budget');
        $spent = Project::sum('spent_amount');
        $custodies = Custody::where('status', 'active')->count();
        
        return "المشاريع: {$projects}، الميزانية الكلية: {$budget}، المصروف: {$spent}، العهد النشطة: {$custodies}";
    }
    
    private function processLocally($query, User $user)
    {
        $query = strtolower($query);
        
        if ($this->isBalanceQuery($query)) {
            return $this->handleBalanceQuery($query, $user);
        }
        
        if ($this->isBudgetQuery($query)) {
            return $this->handleBudgetQuery($query, $user);
        }
        
        if ($this->isExpenseQuery($query)) {
            return $this->handleExpenseQuery($query, $user);
        }
        
        if ($this->isReportQuery($query)) {
            return $this->handleReportQuery($query, $user);
        }
        
        if ($this->isProjectQuery($query)) {
            return $this->handleProjectQuery($query, $user);
        }
        
        return $this->getDefaultResponse();
    }
    
    private function isBalanceQuery($query)
    {
        $keywords = ['رصيد', 'عهدة', 'متبقي', 'باقي'];
        return $this->containsKeywords($query, $keywords);
    }
    
    private function isBudgetQuery($query)
    {
        $keywords = ['ميزانية', 'مصروف', 'إجمالي', 'تجاوز'];
        return $this->containsKeywords($query, $keywords);
    }
    
    private function isExpenseQuery($query)
    {
        $keywords = ['مصروفات', 'فواتير', 'اليوم', 'الأسبوع'];
        return $this->containsKeywords($query, $keywords);
    }
    
    private function isReportQuery($query)
    {
        $keywords = ['تقرير', 'إحصائية', 'ملخص'];
        return $this->containsKeywords($query, $keywords);
    }
    
    private function containsKeywords($query, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($query, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function handleBalanceQuery($query, User $user)
    {
        $custodies = Custody::where('requested_by', $user->id)
            ->where('status', 'active')
            ->get();
            
        if ($custodies->isEmpty()) {
            return [
                'type' => 'balance',
                'message' => 'لا توجد عهد نشطة باسمك حالياً.',
                'data' => []
            ];
        }
        
        $totalAmount = $custodies->sum('amount');
        $totalRemaining = $custodies->sum('remaining_amount');
        
        return [
            'type' => 'balance',
            'message' => "لديك {$custodies->count()} عهدة نشطة بإجمالي {$totalAmount} ر.س، المتبقي {$totalRemaining} ر.س",
            'data' => $custodies->map(function($custody) {
                return [
                    'project' => $custody->project->name,
                    'amount' => $custody->amount,
                    'remaining' => $custody->remaining_amount
                ];
            })
        ];
    }
    
    private function handleBudgetQuery($query, User $user)
    {
        $projects = Project::all();
        $criticalProjects = $projects->filter(function($project) {
            return $project->budget_percentage >= 90;
        });
        
        if ($criticalProjects->isEmpty()) {
            return [
                'type' => 'budget',
                'message' => 'جميع المشاريع ضمن الحدود الآمنة للميزانية.',
                'data' => []
            ];
        }
        
        return [
            'type' => 'budget',
            'message' => "يوجد {$criticalProjects->count()} مشروع يحتاج انتباه في الميزانية",
            'data' => $criticalProjects->map(function($project) {
                return [
                    'name' => $project->name,
                    'percentage' => round($project->budget_percentage, 1),
                    'remaining' => $project->remaining_budget
                ];
            })
        ];
    }
    
    private function handleExpenseQuery($query, User $user)
    {
        $period = 'today';
        if (strpos($query, 'أسبوع') !== false) {
            $period = 'week';
        } elseif (strpos($query, 'شهر') !== false) {
            $period = 'month';
        }
        
        $expenses = $this->getExpensesByPeriod($period);
        
        return [
            'type' => 'expenses',
            'message' => "إجمالي المصروفات " . $this->getPeriodName($period) . ": " . number_format($expenses->sum('amount')) . " ر.س",
            'data' => [
                'count' => $expenses->count(),
                'total' => $expenses->sum('amount'),
                'by_category' => $expenses->groupBy('category.name')->map(function($group) {
                    return $group->sum('amount');
                })
            ]
        ];
    }
    
    private function handleReportQuery($query, User $user)
    {
        $projects = Project::with('expenses')->get();
        
        return [
            'type' => 'report',
            'message' => 'ملخص سريع للنظام',
            'data' => [
                'total_projects' => $projects->count(),
                'active_projects' => $projects->where('status', 'active')->count(),
                'total_budget' => $projects->sum('total_budget'),
                'total_spent' => $projects->sum('spent_amount'),
                'top_spending_project' => $projects->sortByDesc('spent_amount')->first()?->name
            ]
        ];
    }
    
    private function getExpensesByPeriod($period)
    {
        $query = Expense::with('category');
        
        switch ($period) {
            case 'today':
                return $query->whereDate('expense_date', today())->get();
            case 'week':
                return $query->whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()])->get();
            case 'month':
                return $query->whereMonth('expense_date', now()->month)->get();
            default:
                return $query->whereDate('expense_date', today())->get();
        }
    }
    
    private function getPeriodName($period)
    {
        $names = [
            'today' => 'اليوم',
            'week' => 'هذا الأسبوع',
            'month' => 'هذا الشهر'
        ];
        
        return $names[$period] ?? 'اليوم';
    }
    
    private function isProjectQuery($query)
    {
        $keywords = ['مشروع', 'مشاريع', 'حالة المشروع'];
        return $this->containsKeywords($query, $keywords);
    }
    
    private function handleProjectQuery($query, User $user)
    {
        $projects = Project::with('expenses')->get();
        $critical = $projects->where('budget_percentage', '>=', 90);
        
        return [
            'type' => 'projects',
            'message' => "لديك {$projects->count()} مشروع، منها {$critical->count()} يحتاج انتباه",
            'data' => $projects->map(fn($p) => [
                'name' => $p->name,
                'budget' => $p->total_budget,
                'spent' => $p->spent_amount,
                'percentage' => round($p->budget_percentage, 1)
            ])
        ];
    }
    
    public function summarizeProject(Project $project)
    {
        $expenses = $project->expenses()->with('category')->get();
        $topCategory = $expenses->groupBy('expense_category_id')
            ->sortByDesc(fn($g) => $g->sum('amount'))
            ->first();
        
        $summary = "المشروع: {$project->name}\n";
        $summary .= "الميزانية: " . number_format($project->total_budget) . " ر.س\n";
        $summary .= "المصروف: " . number_format($project->spent_amount) . " ر.س ({$project->budget_percentage}%)\n";
        $summary .= "المتبقي: " . number_format($project->remaining_budget) . " ر.س\n";
        
        if ($topCategory) {
            $categoryName = $topCategory->first()->category->name;
            $categoryTotal = $topCategory->sum('amount');
            $summary .= "أعلى فئة صرف: {$categoryName} (" . number_format($categoryTotal) . " ر.س)";
        }
        
        return $summary;
    }
    
    public function generateAutoReport($type, $filters = [])
    {
        switch ($type) {
            case 'daily':
                return $this->generateDailyReport();
            case 'weekly':
                return $this->generateWeeklyReport();
            case 'monthly':
                return $this->generateMonthlyReport();
            default:
                return null;
        }
    }
    
    private function generateDailyReport()
    {
        $expenses = Expense::whereDate('expense_date', today())->with('category')->get();
        
        return [
            'title' => 'تقرير المصروفات اليومية - ' . today()->format('Y-m-d'),
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->sum('amount'),
            'by_category' => $expenses->groupBy('category.name')->map(fn($g) => [
                'count' => $g->count(),
                'amount' => $g->sum('amount')
            ])
        ];
    }
    
    private function generateWeeklyReport()
    {
        $expenses = Expense::whereBetween('expense_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->with('category', 'project')->get();
        
        return [
            'title' => 'تقرير المصروفات الأسبوعية',
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->sum('amount'),
            'by_project' => $expenses->groupBy('project.name')->map(fn($g) => $g->sum('amount')),
            'by_category' => $expenses->groupBy('category.name')->map(fn($g) => $g->sum('amount'))
        ];
    }
    
    private function generateMonthlyReport()
    {
        $expenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->with('category', 'project')->get();
        
        return [
            'title' => 'تقرير المصروفات الشهرية - ' . now()->format('F Y'),
            'total_expenses' => $expenses->count(),
            'total_amount' => $expenses->sum('amount'),
            'by_project' => $expenses->groupBy('project.name')->map(fn($g) => $g->sum('amount')),
            'by_category' => $expenses->groupBy('category.name')->map(fn($g) => $g->sum('amount')),
            'daily_average' => $expenses->sum('amount') / now()->day
        ];
    }
    
    private function getDefaultResponse()
    {
        return [
            'type' => 'help',
            'message' => 'يمكنني مساعدتك في:\n• الاستفسار عن الأرصدة والعهد\n• حالة الميزانيات والمشاريع\n• المصروفات اليومية والأسبوعية\n• إنشاء تقارير سريعة\n\nجرب: "كم رصيدي؟" أو "ما حالة الميزانية؟" أو "مصروفات اليوم"',
            'data' => []
        ];
    }
}
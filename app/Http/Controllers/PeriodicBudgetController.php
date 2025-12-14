<?php

namespace App\Http\Controllers;

use App\Models\PeriodicBudget;
use App\Models\Project;
use App\Models\BudgetAllocation;
use Illuminate\Http\Request;

class PeriodicBudgetController extends Controller
{
    public function index(Project $project)
    {
        $periodicBudgets = $project->periodicBudgets()
            ->with('allocations.category')
            ->orderBy('start_date', 'desc')
            ->paginate(10);
            
        return view('periodic-budgets.index', compact('project', 'periodicBudgets'));
    }

    public function create(Project $project)
    {
        $categories = \App\Models\ExpenseCategory::where('is_active', true)->get();
        return view('periodic-budgets.create', compact('project', 'categories'));
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'period_type' => 'required|in:weekly,monthly,quarterly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_budget' => 'required|numeric|min:0',
            'allocations' => 'required|array',
            'allocations.*.category_id' => 'required|exists:expense_categories,id',
            'allocations.*.amount' => 'required|numeric|min:0'
        ]);

        // التحقق من عدم تجاوز ميزانية المشروع
        $totalAllocated = array_sum(array_column($validated['allocations'], 'amount'));
        if ($totalAllocated > $validated['total_budget']) {
            return back()->withErrors(['total_budget' => 'مجموع التوزيعات يتجاوز الميزانية الإجمالية']);
        }

        $periodicBudget = $project->periodicBudgets()->create([
            'period_type' => $validated['period_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_budget' => $validated['total_budget'],
            'spent_amount' => 0,
            'status' => 'active'
        ]);

        // إنشاء التوزيعات
        foreach ($validated['allocations'] as $allocation) {
            BudgetAllocation::create([
                'project_id' => $project->id,
                'period_budget_id' => $periodicBudget->id,
                'expense_category_id' => $allocation['category_id'],
                'allocated_amount' => $allocation['amount'],
                'spent_amount' => 0
            ]);
        }

        return redirect()->route('periodic-budgets.index', $project)
            ->with('success', 'تم إنشاء الميزانية الدورية بنجاح');
    }

    public function show(Project $project, PeriodicBudget $periodicBudget)
    {
        $periodicBudget->load(['allocations.category', 'allocations.expenses']);
        
        $stats = [
            'total_budget' => $periodicBudget->total_budget,
            'spent_amount' => $periodicBudget->spent_amount,
            'remaining_amount' => $periodicBudget->total_budget - $periodicBudget->spent_amount,
            'percentage_spent' => $periodicBudget->total_budget > 0 ? 
                ($periodicBudget->spent_amount / $periodicBudget->total_budget) * 100 : 0
        ];
        
        return view('periodic-budgets.show', compact('project', 'periodicBudget', 'stats'));
    }

    public function updateSpending(Project $project, PeriodicBudget $periodicBudget)
    {
        // تحديث المبلغ المصروف من المصروفات الفعلية
        $totalSpent = $project->expenses()
            ->whereBetween('expense_date', [$periodicBudget->start_date, $periodicBudget->end_date])
            ->sum('amount');
            
        $periodicBudget->update(['spent_amount' => $totalSpent]);
        
        // تحديث التوزيعات
        foreach ($periodicBudget->allocations as $allocation) {
            $categorySpent = $project->expenses()
                ->where('expense_category_id', $allocation->expense_category_id)
                ->whereBetween('expense_date', [$periodicBudget->start_date, $periodicBudget->end_date])
                ->sum('amount');
                
            $allocation->update(['spent_amount' => $categorySpent]);
        }
        
        return response()->json(['success' => true, 'spent_amount' => $totalSpent]);
    }

    public function getActiveForProject(Project $project)
    {
        $activeBudget = $project->periodicBudgets()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('allocations.category')
            ->first();
            
        return response()->json($activeBudget);
    }
}
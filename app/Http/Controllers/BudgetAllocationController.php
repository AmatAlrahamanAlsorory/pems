<?php

namespace App\Http\Controllers;

use App\Models\BudgetAllocation;
use App\Models\Project;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class BudgetAllocationController extends Controller
{
    public function create(Project $project)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $allocations = $project->budgetAllocations()->with('category')->get();
        
        return view('budget-allocations.create', compact('project', 'categories', 'allocations'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'allocations' => 'required|array',
            'allocations.*.category_id' => 'required|exists:expense_categories,id',
            'allocations.*.percentage' => 'required|numeric|min:0|max:100',
        ]);

        // حذف التوزيعات السابقة
        $project->budgetAllocations()->delete();

        $totalPercentage = 0;
        foreach ($request->allocations as $allocation) {
            $percentage = $allocation['percentage'];
            $amount = ($project->total_budget * $percentage) / 100;
            
            BudgetAllocation::create([
                'project_id' => $project->id,
                'expense_category_id' => $allocation['category_id'],
                'allocated_amount' => $amount,
                'percentage' => $percentage,
            ]);
            
            $totalPercentage += $percentage;
        }

        if ($totalPercentage > 100) {
            return back()->withErrors(['total' => 'إجمالي النسب لا يمكن أن يتجاوز 100%']);
        }

        return redirect()->route('projects.show', $project)->with('success', 'تم توزيع الميزانية بنجاح');
    }

    public function show(Project $project)
    {
        $allocations = $project->budgetAllocations()->with('category')->get();
        return view('budget-allocations.show', compact('project', 'allocations'));
    }
}
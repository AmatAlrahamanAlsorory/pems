<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends BaseController
{
    public function index()
    {
        $projects = Project::select(['id', 'name', 'type', 'status', 'total_budget', 'spent_amount', 'start_date', 'created_at'])
            ->withCount('expenses')
            ->latest()
            ->paginate(10);
        
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('create_project', 'غير مصرح لك بإنشاء مشاريع');
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:series,movie,program',
            'total_budget' => 'required|numeric|min:0',
            'emergency_reserve' => 'nullable|numeric|min:0',
            'planned_days' => 'nullable|integer|min:1',
            'episodes_count' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        $validated['status'] = 'planning';
        $validated['spent_amount'] = 0;

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'تم إنشاء المشروع بنجاح');
    }

    public function show(Project $project)
    {
        $project->load([
            'expenses' => fn($q) => $q->latest()->limit(10)->select(['id', 'project_id', 'expense_category_id', 'amount', 'expense_date']),
            'expenses.category:id,name,name_ar',
            'custodies' => fn($q) => $q->latest()->limit(5)->select(['id', 'project_id', 'amount', 'status', 'created_at'])
        ]);
        
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('edit_project', 'غير مصرح لك بتعديل المشاريع');
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:series,movie,program',
            'total_budget' => 'required|numeric|min:0',
            'emergency_reserve' => 'nullable|numeric|min:0',
            'planned_days' => 'nullable|integer|min:1',
            'episodes_count' => 'nullable|integer|min:1',
            'status' => 'required|in:planning,active,on_hold,completed,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'تم تحديث المشروع بنجاح');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete_project', 'غير مصرح لك بحذف المشاريع');
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'تم حذف المشروع بنجاح');
    }

    public function critical()
    {
        $criticalProjects = Project::where('total_budget', '>', 0)
            ->get()
            ->filter(function ($project) {
                return $project->budget_percentage >= 90;
            })
            ->sortByDesc('budget_percentage');
        
        return view('projects.critical', compact('criticalProjects'));
    }
}

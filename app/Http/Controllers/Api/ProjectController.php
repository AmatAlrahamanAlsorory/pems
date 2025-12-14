<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with(['budgetAllocations.category'])
            ->where('status', 'active')
            ->get();

        return response()->json($projects);
    }

    public function show(Project $project)
    {
        $project->load(['budgetAllocations.category', 'locations', 'expenses.category']);
        
        return response()->json([
            'project' => $project,
            'budget_status' => $project->budget_status,
            'remaining_budget' => $project->remaining_budget,
            'budget_percentage' => $project->budget_percentage,
        ]);
    }
}
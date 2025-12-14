<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;

class CheckBudgetLimit
{
    public function handle(Request $request, Closure $next)
    {
        // فحص المشاريع المحظورة
        if ($request->has('project_id')) {
            $project = Project::find($request->project_id);
            
            if ($project && $project->status === 'blocked') {
                return back()->withErrors([
                    'project' => 'هذا المشروع محظور بسبب تجاوز الميزانية. لا يمكن إضافة مصروفات جديدة.'
                ]);
            }
            
            if ($project && $project->budget_percentage >= 100) {
                return back()->withErrors([
                    'budget' => 'تم تجاوز ميزانية المشروع. لا يمكن إضافة مصروفات جديدة.'
                ]);
            }
        }
        
        return $next($request);
    }
}
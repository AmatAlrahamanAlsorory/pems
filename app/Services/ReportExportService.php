<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportService
{
    public function exportProjectReport($projectId)
    {
        $projects = Project::with(['expenses.category'])
            ->where('id', $projectId)
            ->get();
        
        $pdf = Pdf::loadView('reports.pdf.projects', compact('projects'));
        return $pdf->download("project-report-{$projectId}.pdf");
    }
    
    public function exportExpensesReport($filters = [])
    {
        $query = Expense::with(['project', 'category', 'item', 'user']);
        
        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        
        if (isset($filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['date_to']);
        }
        
        if (isset($filters['category_id'])) {
            $query->where('expense_category_id', $filters['category_id']);
        }
        
        $expenses = $query->orderBy('expense_date', 'desc')->get();
        
        $pdf = Pdf::loadView('reports.pdf.expenses', compact('expenses', 'filters'));
        return $pdf->download('expenses-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ExpenseCategory;
use App\Models\Location;
use App\Models\Expense;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function projectReport(Request $request)
    {
        $projects = Project::select(['id', 'name', 'type', 'total_budget', 'spent_amount', 'status'])
            ->when($request->project_id, fn($q) => $q->where('id', $request->project_id))
            ->get()
            ->map(function($project) {
                $project->remaining_budget = $project->total_budget - $project->spent_amount;
                $project->budget_percentage = $project->total_budget > 0 ? ($project->spent_amount / $project->total_budget) * 100 : 0;
                return $project;
            });

        if ($request->format == 'excel') {
            try {
                return Excel::download(new \App\Exports\ProjectsExport($projects), 'projects-report.xlsx');
            } catch (\Exception $e) {
                return back()->with('error', 'خطأ في تصدير Excel: ' . $e->getMessage());
            }
        }
        
        if ($request->format == 'pdf') {
            try {
                $pdf = PDF::loadView('reports.pdf.projects', compact('projects'))
                    ->setPaper('a4', 'landscape');
                return $pdf->download('projects-report.pdf');
            } catch (\Exception $e) {
                return back()->with('error', 'خطأ في تصدير PDF: ' . $e->getMessage());
            }
        }

        return view('reports.project', compact('projects'));
    }

    public function categoryReport(Request $request)
    {
        $categories = ExpenseCategory::with(['expenses' => function($q) use ($request) {
            $q->where('status', 'approved');
            if ($request->project_id) $q->where('project_id', $request->project_id);
            if ($request->date_from) $q->where('expense_date', '>=', $request->date_from);
            if ($request->date_to) $q->where('expense_date', '<=', $request->date_to);
        }])->get();

        $totalSpent = $categories->sum(fn($cat) => $cat->expenses->sum('amount'));

        return view('reports.category', compact('categories', 'totalSpent'));
    }

    public function comparisonReport(Request $request)
    {
        $project = Project::with(['budgetAllocations.category'])->findOrFail($request->project_id);
        
        $actualSpending = DB::table('expenses')
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->where('expenses.project_id', $project->id)
            ->where('expenses.status', 'approved')
            ->select('expense_categories.name', 'expense_categories.color', 
                    DB::raw('SUM(expenses.amount) as actual_amount'))
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->get();

        return view('reports.comparison', compact('project', 'actualSpending'));
    }

    public function locationReport(Request $request)
    {
        $locations = Location::with(['project', 'expenses' => function($q) {
            $q->where('status', 'approved');
        }])->get();

        return view('reports.location', compact('locations'));
    }

    public function custodyReport(Request $request)
    {
        $custodies = \App\Models\Custody::with(['project', 'user', 'location'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.custody', compact('custodies'));
    }

    public function personReport(Request $request)
    {
        $people = \App\Models\Person::with(['expenses' => function($q) use ($request) {
            $q->where('status', 'approved');
            if ($request->project_id) $q->where('project_id', $request->project_id);
            if ($request->date_from) $q->where('expense_date', '>=', $request->date_from);
            if ($request->date_to) $q->where('expense_date', '<=', $request->date_to);
        }])->get();

        return view('reports.person', compact('people'));
    }

    public function dashboardData()
    {
        $data = [
            'monthly_expenses' => Expense::where('status', 'approved')
                ->selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
                ->whereYear('expense_date', date('Y'))
                ->groupBy('month')
                ->pluck('total', 'month'),
            
            'category_spending' => ExpenseCategory::with(['expenses' => function($q) {
                $q->where('status', 'approved');
            }])->get()->map(function($cat) {
                return [
                    'name' => $cat->name,
                    'color' => $cat->color,
                    'amount' => $cat->expenses->sum('amount')
                ];
            })->where('amount', '>', 0),
            
            'project_status' => Project::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN (spent_amount / total_budget) * 100 >= 90 THEN 1 ELSE 0 END) as critical,
                SUM(CASE WHEN (spent_amount / total_budget) * 100 BETWEEN 70 AND 89 THEN 1 ELSE 0 END) as warning,
                SUM(CASE WHEN (spent_amount / total_budget) * 100 < 70 THEN 1 ELSE 0 END) as normal
            ')->first()
        ];

        return response()->json($data);
    }
    
    public function exportProject(Request $request)
    {
        $exportService = app(ReportExportService::class);
        
        if ($request->project_id) {
            return $exportService->exportProjectReport($request->project_id);
        }
        
        return back()->withErrors(['project_id' => 'يرجى اختيار مشروع للتصدير']);
    }
    
    public function exportExpenses(Request $request)
    {
        $exportService = app(ReportExportService::class);
        
        $filters = $request->only(['project_id', 'date_from', 'date_to', 'category_id']);
        
        return $exportService->exportExpensesReport($filters);
    }
    
    public function exportCustodies(Request $request)
    {
        $query = \App\Models\Custody::with(['project']);
        
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $custodies = $query->get();
        
        if ($request->format === 'pdf') {
            try {
                $pdf = PDF::loadView('reports.pdf.custodies', compact('custodies'))
                    ->setPaper('a4', 'portrait')
                    ->setOption('isHtml5ParserEnabled', true)
                    ->setOption('isRemoteEnabled', true);
                return $pdf->download('custodies-report-' . now()->format('Y-m-d') . '.pdf');
            } catch (\Exception $e) {
                return back()->with('error', 'خطأ في تصدير PDF: ' . $e->getMessage());
            }
        }
        
        try {
            return Excel::download(
                new \App\Exports\CustodiesExport($custodies),
                'custodies-report-' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'خطأ في تصدير Excel: ' . $e->getMessage());
        }
    }
    
    public function exceptionsReport(Request $request)
    {
        $exceptions = collect();
        
        // المصروفات بدون فواتير
        $noInvoice = Expense::whereNull('invoice_file')
            ->with(['project', 'category', 'item', 'user'])
            ->get()
            ->map(fn($expense) => [
                'type' => 'no_invoice',
                'title' => 'مصروف بدون فاتورة',
                'expense' => $expense,
                'severity' => 'high'
            ]);
        
        // المصروفات المشبوهة (مبالغ غير عادية)
        $avgAmount = Expense::avg('amount');
        $suspicious = Expense::where('amount', '>', $avgAmount * 3)
            ->with(['project', 'category', 'item', 'user'])
            ->get()
            ->map(fn($expense) => [
                'type' => 'suspicious_amount',
                'title' => 'مبلغ مشبوه (أعلى من المتوسط بـ 3 مرات)',
                'expense' => $expense,
                'severity' => 'medium'
            ]);
        
        // المصروفات المتأخرة في الإدخال
        $lateEntry = Expense::whereRaw('DATEDIFF(created_at, expense_date) > 7')
            ->with(['project', 'category', 'item', 'user'])
            ->get()
            ->map(fn($expense) => [
                'type' => 'late_entry',
                'title' => 'إدخال متأخر (أكثر من 7 أيام)',
                'expense' => $expense,
                'severity' => 'low'
            ]);
        
        $exceptions = $exceptions->merge($noInvoice)
                                ->merge($suspicious)
                                ->merge($lateEntry)
                                ->sortByDesc('severity');
        
        return view('reports.exceptions', compact('exceptions'));
    }
    
    public function printProjectReport(Request $request)
    {
        $projects = Project::select(['id', 'name', 'type', 'total_budget', 'spent_amount', 'status'])
            ->when($request->project_id, fn($q) => $q->where('id', $request->project_id))
            ->get();
            
        return view('reports.projects-print', compact('projects'));
    }
}
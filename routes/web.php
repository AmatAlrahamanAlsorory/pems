<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $stats = Cache::remember('dashboard_stats_' . auth()->id(), 60, function () {
        return [
            'projects_count' => \App\Models\Project::count(),
            'total_budget' => \App\Models\Project::sum('total_budget'),
            'active_custodies' => \App\Models\Custody::where('status', 'active')->count(),
            'today_expenses' => \App\Models\Expense::whereDate('created_at', today())->count(),
            'total_spent' => \App\Models\Expense::where('status', 'approved')->sum('amount'),
            'pending_expenses' => \App\Models\Expense::where('status', 'pending')->count(),
        ];
    });
    
    // بيانات الرسوم البيانية
    $chartData = [
        'project_status' => [
            'normal' => \App\Models\Project::whereRaw('(CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100) < 70')->count(),
            'warning' => \App\Models\Project::whereRaw('(CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100) >= 70 AND (CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100) < 90')->count(),
            'critical' => \App\Models\Project::whereRaw('(CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100) >= 90')->count(),
        ],
        'category_spending' => \DB::table('expenses')
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.name', \DB::raw('SUM(CAST(expenses.amount AS REAL)) as amount'))
            ->where('expenses.status', 'approved')
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderBy('amount', 'desc')
            ->limit(10)
            ->get()
            ->toArray(),
        'monthly_expenses' => \DB::table('expenses')
            ->select(\DB::raw('strftime(\'%m\', expense_date) as month'), \DB::raw('SUM(CAST(amount AS REAL)) as amount'))
            ->where('status', 'approved')
            ->whereRaw('strftime(\'%Y\', expense_date) = ?', [date('Y')])
            ->groupBy(\DB::raw('strftime(\'%m\', expense_date)'))
            ->pluck('amount', 'month')
            ->toArray()
    ];
    
    $notifications = \App\Models\Notification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->latest()
        ->limit(5)
        ->get();
        
    $criticalProjects = \App\Models\Project::whereRaw('CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100 >= 90')
        ->selectRaw('*, CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100 as budget_percentage')
        ->limit(5)
        ->get();
        
    $pendingApprovals = \App\Models\Approval::where('status', 'pending')
        ->with(['approvable'])
        ->whereHas('approvable')
        ->limit(5)
        ->get()
        ->each(function($approval) {
            if ($approval->approvable_type === 'App\\Models\\Expense') {
                $approval->approvable->load('expenseCategory');
            } elseif ($approval->approvable_type === 'App\\Models\\Custody') {
                $approval->approvable->load('project');
            }
        });
    
    return view('dashboard', compact('stats', 'chartData', 'notifications', 'criticalProjects', 'pendingApprovals'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // === إدارة المستخدمين ===
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    });
    
    Route::middleware('permission:edit_user')->group(function () {
        Route::get('users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    });
    
    Route::middleware('permission:delete_user')->group(function () {
        Route::delete('users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // === إدارة المشاريع ===
    Route::middleware('permission:create_project')->group(function () {
        Route::get('projects/create', [\App\Http\Controllers\ProjectController::class, 'create'])->name('projects.create');
        Route::post('projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    });
    
    Route::middleware('permission:edit_project')->group(function () {
        Route::get('projects/{project}/edit', [\App\Http\Controllers\ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    });
    
    Route::middleware('permission:delete_project')->group(function () {
        Route::delete('projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');
    });
    
    // === إدارة المواقع والأشخاص ===
    // خريطة المواقع (يجب أن تكون قبل resource)
    Route::middleware('permission:manage_locations')->group(function () {
        Route::get('locations/map', [\App\Http\Controllers\LocationController::class, 'map'])->name('locations.map');
        Route::get('locations', [\App\Http\Controllers\LocationController::class, 'index'])->name('locations.index');
        Route::get('locations/{location}', [\App\Http\Controllers\LocationController::class, 'show'])->name('locations.show');
    });
    
    Route::middleware('permission:manage_locations')->group(function () {
        Route::get('locations/create', [\App\Http\Controllers\LocationController::class, 'create'])->name('locations.create');
        Route::post('locations', [\App\Http\Controllers\LocationController::class, 'store'])->name('locations.store');
    });
    
    Route::middleware('permission:edit_location')->group(function () {
        Route::get('locations/{location}/edit', [\App\Http\Controllers\LocationController::class, 'edit'])->name('locations.edit');
        Route::put('locations/{location}', [\App\Http\Controllers\LocationController::class, 'update'])->name('locations.update');
    });
    
    Route::middleware('permission:delete_location')->group(function () {
        Route::delete('locations/{location}', [\App\Http\Controllers\LocationController::class, 'destroy'])->name('locations.destroy');
    });
    
    Route::middleware('permission:manage_people')->group(function () {
        Route::get('people', [\App\Http\Controllers\PersonController::class, 'index'])->name('people.index');
        Route::get('people/create', [\App\Http\Controllers\PersonController::class, 'create'])->name('people.create');
        Route::post('people', [\App\Http\Controllers\PersonController::class, 'store'])->name('people.store');
        Route::get('people/{person}', [\App\Http\Controllers\PersonController::class, 'show'])->name('people.show');
    });
    
    Route::middleware('permission:edit_person')->group(function () {
        Route::get('people/{person}/edit', [\App\Http\Controllers\PersonController::class, 'edit'])->name('people.edit');
        Route::put('people/{person}', [\App\Http\Controllers\PersonController::class, 'update'])->name('people.update');
    });
    
    Route::middleware('permission:delete_person')->group(function () {
        Route::delete('people/{person}', [\App\Http\Controllers\PersonController::class, 'destroy'])->name('people.destroy');
    });
    
    // === إدارة الميزانيات ===
    Route::middleware('permission:manage_users')->group(function () {
        Route::get('projects/{project}/budget-allocations/create', [\App\Http\Controllers\BudgetAllocationController::class, 'create'])->name('budget-allocations.create');
        Route::post('projects/{project}/budget-allocations', [\App\Http\Controllers\BudgetAllocationController::class, 'store'])->name('budget-allocations.store');
        
        // الميزانيات الدورية
        Route::get('projects/{project}/periodic-budgets', [\App\Http\Controllers\PeriodicBudgetController::class, 'index'])->name('periodic-budgets.index');
        Route::get('projects/{project}/periodic-budgets/create', [\App\Http\Controllers\PeriodicBudgetController::class, 'create'])->name('periodic-budgets.create');
        Route::post('projects/{project}/periodic-budgets', [\App\Http\Controllers\PeriodicBudgetController::class, 'store'])->name('periodic-budgets.store');
        Route::get('projects/{project}/periodic-budgets/{periodicBudget}', [\App\Http\Controllers\PeriodicBudgetController::class, 'show'])->name('periodic-budgets.show');
        Route::post('projects/{project}/periodic-budgets/{periodicBudget}/update-spending', [\App\Http\Controllers\PeriodicBudgetController::class, 'updateSpending'])->name('periodic-budgets.update-spending');
        Route::get('projects/{project}/periodic-budgets-active', [\App\Http\Controllers\PeriodicBudgetController::class, 'getActiveForProject'])->name('periodic-budgets.active');
    });
    
    // === موافقة العهد ===
    Route::middleware('permission:approve_custody')->group(function () {
        Route::post('custodies/{custody}/approve', [\App\Http\Controllers\CustodyController::class, 'approve'])->name('custodies.approve');
    });
    
    // === عرض المشاريع ===
    Route::middleware('permission:view_project')->group(function () {
        Route::get('projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
        Route::get('projects-critical', [\App\Http\Controllers\ProjectController::class, 'critical'])->name('projects.critical');
        Route::post('locations/{location}/gps', [\App\Http\Controllers\LocationController::class, 'updateGPS'])->name('locations.gps');
    });
    
    // === إدارة العهد ===
    Route::get('custodies/create', [\App\Http\Controllers\CustodyController::class, 'create'])->name('custodies.create');
    Route::post('custodies', [\App\Http\Controllers\CustodyController::class, 'store'])
        ->middleware('budget.check')
        ->name('custodies.store');
    
    Route::middleware('permission:edit_custody')->group(function () {
        Route::get('custodies/{custody}/edit', [\App\Http\Controllers\CustodyController::class, 'edit'])->name('custodies.edit');
        Route::put('custodies/{custody}', [\App\Http\Controllers\CustodyController::class, 'update'])->name('custodies.update');
    });
    
    Route::middleware('permission:delete_custody')->group(function () {
        Route::delete('custodies/{custody}', [\App\Http\Controllers\CustodyController::class, 'destroy'])->name('custodies.destroy');
    });
    
    // === موافقة المصروفات ===
    Route::middleware('permission:approve_expense')->group(function () {
        Route::post('expenses/{expense}/approve', [\App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
    });
    
    // === إدارة المصروفات ===
    Route::get('expenses/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])
        ->middleware('budget.check')
        ->name('expenses.store');
    
    Route::middleware('permission:edit_expense')->group(function () {
        Route::get('expenses/{expense}/edit', [\App\Http\Controllers\ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
    });
    
    Route::middleware('permission:delete_expense')->group(function () {
        Route::delete('expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });
    
    // === عرض البيانات ===
    Route::middleware('permission:view_custody')->group(function () {
        Route::get('custodies', [\App\Http\Controllers\CustodyController::class, 'index'])->name('custodies.index');
        Route::get('custodies/{custody}', [\App\Http\Controllers\CustodyController::class, 'show'])->name('custodies.show');
        Route::post('custodies/{custody}/settle', [\App\Http\Controllers\CustodyController::class, 'settle'])->name('custodies.settle');
    });
    
    Route::middleware('permission:view_expense')->group(function () {
        Route::get('expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
    });
    
    Route::middleware('permission:view_project')->group(function () {
        Route::get('projects/{project}/budget-allocations', [\App\Http\Controllers\BudgetAllocationController::class, 'show'])->name('budget-allocations.show');
    });
    
    // === الإشعارات ===
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // === التقارير ===
    Route::middleware('permission:view_reports')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
            Route::get('project', [\App\Http\Controllers\ReportController::class, 'projectReport'])->name('project');
            Route::get('category', [\App\Http\Controllers\ReportController::class, 'categoryReport'])->name('category');
            Route::get('comparison', [\App\Http\Controllers\ReportController::class, 'comparisonReport'])->name('comparison');
            Route::get('location', [\App\Http\Controllers\ReportController::class, 'locationReport'])->name('location');
            Route::get('custody', [\App\Http\Controllers\ReportController::class, 'custodyReport'])->name('custody');
            Route::get('person', [\App\Http\Controllers\ReportController::class, 'personReport'])->name('person');
            Route::get('dashboard-data', function() {
                return response()->json([
                    'project_status' => [
                        'normal' => \App\Models\Project::whereRaw('CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100 < 70')->count(),
                        'warning' => \App\Models\Project::whereRaw('CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100 >= 70 AND CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100 < 90')->count(),
                        'critical' => \App\Models\Project::whereRaw('CAST(spent_amount AS REAL) / CAST(total_budget AS REAL) * 100 >= 90')->count(),
                    ],
                    'category_spending' => \DB::table('expenses')
                        ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                        ->select('expense_categories.name', \DB::raw('SUM(expenses.amount) as amount'))
                        ->where('expenses.status', 'approved')
                        ->groupBy('expense_categories.id', 'expense_categories.name')
                        ->orderBy('amount', 'desc')
                        ->get(),
                    'monthly_expenses' => \DB::table('expenses')
                        ->select(\DB::raw('strftime(\'%m\', expense_date) as month'), \DB::raw('SUM(amount) as amount'))
                        ->where('status', 'approved')
                        ->whereRaw('strftime(\'%Y\', expense_date) = ?', [date('Y')])
                        ->groupBy(\DB::raw('strftime(\'%m\', expense_date)'))
                        ->pluck('amount', 'month')
                ]);
            })->name('dashboard-data');
            Route::get('project/print', [\App\Http\Controllers\ReportController::class, 'printProjectReport'])->name('project.print');
        });
    });
    
    // === تقرير الاستثناءات ===
    Route::middleware('permission:view_exceptions')->group(function () {
        Route::get('reports/exceptions', [\App\Http\Controllers\ReportController::class, 'exceptionsReport'])->name('reports.exceptions');
    });
    
    // === تصدير التقارير ===
    Route::middleware('permission:export_reports')->group(function () {
        Route::get('reports/project/export', [\App\Http\Controllers\ReportController::class, 'exportProject'])->name('reports.project.export');
        Route::get('reports/expenses/export', [\App\Http\Controllers\ReportController::class, 'exportExpenses'])->name('reports.expenses.export');
        Route::get('reports/custodies/export', [\App\Http\Controllers\ReportController::class, 'exportCustodies'])->name('reports.custodies.export');
    });
    
    // === الموافقات ===
    Route::middleware('permission:approve_custody')->group(function () {
        Route::get('approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
        Route::post('approvals/{approvalId}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('approvals/{approvalId}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');
    });
});

require __DIR__.'/auth.php';

// Two Factor Authentication
Route::middleware('auth')->group(function () {
    Route::get('/2fa/verify', [\App\Http\Controllers\TwoFactorController::class, 'show'])->name('2fa.verify');
    Route::post('/2fa/verify', [\App\Http\Controllers\TwoFactorController::class, 'verify']);
    Route::post('/2fa/send', [\App\Http\Controllers\TwoFactorController::class, 'send'])->name('2fa.send');
});

// اختبار قاعدة البيانات
Route::get('/test-db', function () {
    try {
        $connection = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $result = \Illuminate\Support\Facades\DB::select('SELECT version() as version');
        
        return response()->json([
            'status' => 'success',
            'message' => 'تم الاتصال بنجاح مع Supabase!',
            'database_version' => $result[0]->version
        ]);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'فشل الاتصال: ' . $e->getMessage()
        ]);
    }
});

// API Routes
Route::get('/api/categories/{category}/items', function($categoryId) {
    return \App\Models\ExpenseItem::where('expense_category_id', $categoryId)->get();
});

Route::middleware('auth')->group(function () {
    Route::get('/api/notifications/unread', function() {
        $notifications = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->get();
            
        return response()->json([
            'count' => $notifications->count(),
            'latest' => $notifications->first(),
            'notifications' => $notifications
        ]);
    });
    
    Route::post('/api/notifications/{notification}/read', function($id) {
        $notification = \App\Models\Notification::where('user_id', auth()->id())
            ->findOrFail($id);
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    });
    
    Route::get('/api/search', [\App\Http\Controllers\SearchController::class, 'search']);
    Route::post('/api/ocr/process', function(\Illuminate\Http\Request $request) {
        $request->validate(['image' => 'required|image|max:5120']);
        
        $path = $request->file('image')->store('temp-ocr');
        $ocrService = app(\App\Services\OCRService::class);
        $data = $ocrService->extractInvoiceData($path);
        
        \Illuminate\Support\Facades\Storage::delete($path);
        
        return response()->json($data);
    });
    
    // AI Routes
    Route::post('/api/ai/assistant', [\App\Http\Controllers\AIController::class, 'assistant']);
    Route::get('/api/ai/predict/{project}', [\App\Http\Controllers\AIController::class, 'predictBudget']);
    Route::get('/api/ai/insights/{project}', [\App\Http\Controllers\AIController::class, 'projectInsights']);
    Route::get('/api/ai/fraud', [\App\Http\Controllers\AIController::class, 'fraudReport']);
    Route::get('/api/ai/report', [\App\Http\Controllers\AIController::class, 'autoReport']);
    
    // Predictive Analytics Routes
    Route::prefix('api/analytics')->group(function () {
        Route::get('/{project}/weekly-prediction', [\App\Http\Controllers\PredictiveAnalyticsController::class, 'weeklyPrediction']);
        Route::get('/{project}/budget-overrun', [\App\Http\Controllers\PredictiveAnalyticsController::class, 'budgetOverrunPrediction']);
        Route::get('/{project}/seasonal-analysis', [\App\Http\Controllers\PredictiveAnalyticsController::class, 'seasonalAnalysis']);
        Route::get('/{project}/cash-flow', [\App\Http\Controllers\PredictiveAnalyticsController::class, 'cashFlowPrediction']);
        Route::get('/{project}/efficiency', [\App\Http\Controllers\PredictiveAnalyticsController::class, 'spendingEfficiency']);
        Route::get('/{project}/dashboard', [\App\Http\Controllers\PredictiveAnalyticsController::class, 'dashboard']);
    });
    
    // Offline Sync Routes
    Route::post('/api/expenses/sync', function(\Illuminate\Http\Request $request) {
        $expense = \App\Models\Expense::create($request->all());
        return response()->json(['success' => true, 'expense' => $expense]);
    });
    
    Route::get('/api/categories', function() {
        return \App\Models\ExpenseCategory::with('items')->get();
    });
    
    Route::get('/api/custodies/active', function() {
        return \App\Models\Custody::where('status', 'active')
            ->where('requested_by', auth()->id())
            ->with('project')
            ->get();
    });
    
    Route::get('/ai/assistant', function() {
        return view('ai.assistant');
    })->name('ai.assistant');
    
    Route::get('/ai/analytics', function() {
        $projects = \App\Models\Project::all();
        return view('ai.analytics', compact('projects'));
    })->name('ai.analytics');
    
    Route::get('/ai/fraud', function() {
        return view('ai.fraud');
    })->name('ai.fraud');
    
    Route::get('/api/invoice/{expense}/view', function($expenseId) {
        $expense = \App\Models\Expense::findOrFail($expenseId);
        
        if (!$expense->invoice_file) {
            abort(404, 'لا توجد فاتورة');
        }
        
        $encryptionService = app(\App\Services\FileEncryptionService::class);
        return $encryptionService->serveEncryptedFile($expense->invoice_file, 'invoice.pdf');
    });
    
    // تصدير سريع
    Route::get('/api/export/expenses', function(\Illuminate\Http\Request $request) {
        $query = \App\Models\Expense::with(['project', 'category']);
        
        if ($request->project_id) $query->where('project_id', $request->project_id);
        if ($request->date_from) $query->whereDate('expense_date', '>=', $request->date_from);
        if ($request->date_to) $query->whereDate('expense_date', '<=', $request->date_to);
        
        $expenses = $query->get();
        
        return \Excel::download(
            new \App\Exports\ExpensesExport($expenses, $request->only(['include_project', 'include_category'])),
            'expenses-' . now()->format('Y-m-d') . '.xlsx'
        );
    });
    
    Route::get('/api/export/custodies', function(\Illuminate\Http\Request $request) {
        $query = \App\Models\Custody::with(['project', 'requestedBy']);
        
        if ($request->project_id) $query->where('project_id', $request->project_id);
        if ($request->status) $query->where('status', $request->status);
        
        $custodies = $query->get();
        
        return \Excel::download(
            new \App\Exports\CustodiesExport($custodies),
            'custodies-' . now()->format('Y-m-d') . '.xlsx'
        );
    });
    
    Route::get('/api/custody/stats', [\App\Http\Controllers\CustodyController::class, 'stats']);
    Route::get('/custodies/stats', function() {
        return view('custodies.stats');
    })->name('custodies.stats');
    Route::get('/api/periodic-budgets/check', [\App\Http\Controllers\PeriodicBudgetController::class, 'checkPeriodic']);
    
    // واجهة التحليلات التنبؤية
    Route::middleware('permission:view_reports')->group(function () {
        Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/{project}', [\App\Http\Controllers\AnalyticsController::class, 'predictive'])->name('analytics.predictive');
    });
    
    // الرسوم البيانية المحسنة
    Route::get('/enhanced-charts', function() {
        return view('enhanced-charts');
    })->name('enhanced-charts');
    
    Route::get('/offline', function() {
        return view('offline');
    })->name('offline');
    

    
    // API للمواقع
    Route::post('/api/locations', function(\Illuminate\Http\Request $request) {
        $location = \App\Models\Location::create($request->all());
        return response()->json($location->load('project'));
    });
    
    // API للذكاء الاصطناعي المتقدم
    Route::prefix('api/ai-advanced')->group(function () {
        Route::get('/{project}/insights', function($projectId) {
            $service = app(\App\Services\AdvancedAIService::class);
            return response()->json($service->generateSmartInsights($projectId));
        });
        
        Route::get('/{project}/ml-prediction', function($projectId) {
            $service = app(\App\Services\AdvancedAIService::class);
            return response()->json($service->predictWithML($projectId));
        });
        
        Route::post('/fraud-detection', function(\Illuminate\Http\Request $request) {
            $service = app(\App\Services\AdvancedAIService::class);
            $expenses = \App\Models\Expense::whereIn('id', $request->expense_ids)->get();
            return response()->json($service->detectFraudWithAI($expenses));
        });
        
        Route::get('/{project}/budget-optimization', function($projectId) {
            $service = app(\App\Services\AdvancedAIService::class);
            return response()->json($service->optimizeBudgetAllocation($projectId));
        });
    });
    
    // API للـ OCR المحسن
    Route::post('/api/ocr/enhanced', function(\Illuminate\Http\Request $request) {
        $request->validate(['image' => 'required|image|max:10240']);
        
        $path = $request->file('image')->store('temp-ocr');
        $ocrService = app(\App\Services\EnhancedOCRService::class);
        $data = $ocrService->extractInvoiceData($path);
        
        \Illuminate\Support\Facades\Storage::delete($path);
        
        return response()->json($data);
    });
    
    // API للعمل أوفلاين المحسن
    Route::get('/api/offline/stats', function() {
        return response()->json(['status' => 'available']);
    });
    
    Route::get('/api/ping', function() {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });
    
    // التوقيع الرقمي
    Route::post('/api/digital-signature/sign', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\DigitalSignatureService::class);
        return response()->json($service->signDocument($request->document_path, auth()->id()));
    });
    
    Route::post('/api/digital-signature/verify', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\DigitalSignatureService::class);
        return response()->json($service->verifySignature($request->document_path, $request->signature_data));
    });
    
    // البلوك تشين
    Route::get('/api/blockchain/verify', function() {
        $service = app(\App\Services\BlockchainService::class);
        return response()->json($service->verifyChainIntegrity());
    });
    
    Route::get('/api/blockchain/stats', function() {
        $service = app(\App\Services\BlockchainService::class);
        return response()->json($service->getBlockchainStats());
    });
    
    // تحليلات الفيديو
    Route::post('/api/video/analyze', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\VideoAnalyticsService::class);
        return response()->json($service->analyzeProductionVideo($request->video_path));
    });
    
    // التعرف على الوجوه
    Route::post('/api/face/register', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\FaceRecognitionService::class);
        return response()->json($service->registerEmployee($request->employee_id, $request->photo_path));
    });
    
    Route::post('/api/face/attendance', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\FaceRecognitionService::class);
        return response()->json($service->markAttendance($request->photo_path, $request->location_id));
    });
    
    // الأمان المتقدم
    Route::get('/api/security/scan', function() {
        $service = app(\App\Services\AdvancedSecurityService::class);
        return response()->json($service->runSecurityScan());
    });
    
    Route::get('/api/security/gdpr-compliance', function() {
        $service = app(\App\Services\AdvancedSecurityService::class);
        return response()->json($service->ensureGDPRCompliance());
    });
    
    // تحسين الأداء
    Route::get('/api/performance/metrics', function() {
        $service = app(\App\Services\PerformanceOptimizationService::class);
        return response()->json($service->getPerformanceMetrics());
    });
    
    Route::post('/api/performance/optimize', function() {
        $service = app(\App\Services\PerformanceOptimizationService::class);
        $service->optimizeExpenseQueries();
        return response()->json(['success' => true]);
    });
    
    // التقارير المتقدمة
    Route::get('/api/reports/executive-dashboard', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\AdvancedReportingService::class);
        return response()->json($service->generateExecutiveDashboard($request->date_range));
    });
    
    Route::post('/api/reports/ai-report', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\AdvancedReportingService::class);
        return response()->json($service->generateAIReport($request->query, $request->project_id));
    });
    
    Route::get('/api/reports/performance-comparison', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\AdvancedReportingService::class);
        return response()->json($service->generatePerformanceComparison($request->projects));
    });
    
    // التكامل مع الأنظمة الخارجية
    Route::post('/api/integration/sap/sync', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\ExternalIntegrationService::class);
        return response()->json($service->syncWithSAP($request->expense_data));
    });
    
    Route::post('/api/integration/bank/payment', function(\Illuminate\Http\Request $request) {
        $service = app(\App\Services\ExternalIntegrationService::class);
        return response()->json($service->processPayment($request->payment_data));
    });
});

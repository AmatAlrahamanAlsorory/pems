<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
        ];
    });
    
    $notifications = \App\Models\Notification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->latest()
        ->limit(5)
        ->get();
        
    $criticalProjects = \App\Models\Project::whereRaw('(spent_amount / total_budget * 100) >= 90')
        ->limit(5)
        ->get();
        
    $pendingApprovals = \App\Models\Approval::where('status', 'pending')
        ->with('approvable')
        ->limit(5)
        ->get();
    
    return view('dashboard', compact('stats', 'notifications', 'criticalProjects', 'pendingApprovals'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // === إدارة المستخدمين ===
    Route::middleware('permission:manage_users')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
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
    Route::middleware('permission:manage_locations')->group(function () {
        Route::resource('locations', \App\Http\Controllers\LocationController::class);
    });
    
    Route::middleware('permission:manage_people')->group(function () {
        Route::resource('people', \App\Http\Controllers\PersonController::class);
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
        Route::get('locations/map', [\App\Http\Controllers\LocationController::class, 'map'])->name('locations.map');
        Route::post('locations/{location}/gps', [\App\Http\Controllers\LocationController::class, 'updateGPS'])->name('locations.gps');
    });
    
    // === إدارة العهد ===
    Route::get('custodies/create', [\App\Http\Controllers\CustodyController::class, 'create'])->name('custodies.create');
    Route::post('custodies', [\App\Http\Controllers\CustodyController::class, 'store'])
        ->middleware('budget.check')
        ->name('custodies.store');
    
    // === موافقة المصروفات ===
    Route::middleware('permission:approve_expense')->group(function () {
        Route::post('expenses/{expense}/approve', [\App\Http\Controllers\ExpenseController::class, 'approve'])->name('expenses.approve');
    });
    
    // === إدارة المصروفات ===
    Route::get('expenses/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])
        ->middleware('budget.check')
        ->name('expenses.store');
    
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
            Route::get('dashboard-data', [\App\Http\Controllers\ReportController::class, 'dashboardData'])->name('dashboard-data');
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
    Route::get('/analytics/{project}', function($projectId) {
        $project = \App\Models\Project::findOrFail($projectId);
        return view('analytics.predictive', compact('project'));
    })->name('analytics.predictive');
    
    Route::get('/offline', function() {
        return view('offline');
    })->name('offline');
    
    // خريطة المواقع التفاعلية
    Route::get('/locations/map', function() {
        $locations = \App\Models\Location::with(['project', 'expenses'])
            ->get()
            ->map(function($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'address' => $location->address,
                    'status' => $location->status ?? 'active',
                    'project_id' => $location->project_id,
                    'project' => $location->project,
                    'budget' => $location->budget ?? 0,
                    'spent_amount' => $location->expenses->sum('amount'),
                    'expenses_count' => $location->expenses->count(),
                    'last_activity' => $location->expenses->max('expense_date')
                ];
            });
            
        $projects = \App\Models\Project::all();
        
        return view('locations.map', compact('locations', 'projects'));
    })->name('locations.map');
    
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

<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\Approval;
use App\Models\Notification;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RealDashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "🚀 بدء إضافة البيانات الحقيقية للوحة التحكم...\n";

        // إنشاء مشاريع حرجة
        $this->createCriticalProjects();
        
        // إنشاء موافقات معلقة
        $this->createPendingApprovals();
        
        // إنشاء تنبيهات مهمة
        $this->createImportantNotifications();
        
        echo "✅ تم إضافة جميع البيانات الحقيقية بنجاح!\n";
    }

    private function createCriticalProjects()
    {
        echo "📊 إنشاء مشاريع حرجة...\n";

        $criticalProjects = [
            [
                'name' => 'مسلسل الأحلام الذهبية',
                'description' => 'مسلسل درامي من 30 حلقة - الموسم الثاني',
                'total_budget' => 2500000,
                'spent_percentage' => 0.95, // 95% مستنفد
                'start_date' => Carbon::now()->subMonths(8),
                'end_date' => Carbon::now()->addMonth(1),
            ],
            [
                'name' => 'برنامج المواهب الجديدة',
                'description' => 'برنامج مسابقات أسبوعي - 12 حلقة',
                'total_budget' => 1800000,
                'spent_percentage' => 0.88, // 88% مستنفد
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(2),
            ],
            [
                'name' => 'فيلم الأكشن الكبير',
                'description' => 'فيلم سينمائي بميزانية ضخمة',
                'total_budget' => 5000000,
                'spent_percentage' => 0.92, // 92% مستنفد
                'start_date' => Carbon::now()->subYear(),
                'end_date' => Carbon::now()->addMonths(3),
            ]
        ];

        foreach ($criticalProjects as $projectData) {
            $project = Project::create([
                'name' => $projectData['name'],
                'description' => $projectData['description'],
                'total_budget' => $projectData['total_budget'],
                'start_date' => $projectData['start_date'],
                'end_date' => $projectData['end_date'],
                'status' => 'active',
                'currency' => 'SAR'
            ]);

            // حساب المبلغ المصروف
            $spentAmount = $projectData['total_budget'] * $projectData['spent_percentage'];
            $project->spent_amount = $spentAmount;
            $project->save();

            $percentage = $projectData['spent_percentage'] * 100;
            echo "  ✓ تم إنشاء مشروع حرج: {$project->name} ({$percentage}% مستنفد)\n";
        }
    }

    private function createPendingApprovals()
    {
        echo "⏳ إنشاء موافقات معلقة...\n";

        // الحصول على فئات المصروفات
        $categories = ExpenseCategory::all();
        if ($categories->isEmpty()) {
            echo "  ⚠️ لا توجد فئات مصروفات، سيتم إنشاؤها...\n";
            $this->createExpenseCategories();
            $categories = ExpenseCategory::all();
        }

        // إنشاء مصروفات تحتاج موافقة
        $pendingExpenses = [
            [
                'description' => 'تأجير معدات تصوير احترافية - كاميرات 4K',
                'amount' => 45000,
                'category' => 'معدات التصوير',
                'project_name' => 'مسلسل الأحلام الذهبية'
            ],
            [
                'description' => 'أجور الممثلين الضيوف - الحلقة الخاصة',
                'amount' => 85000,
                'category' => 'أجور الممثلين',
                'project_name' => 'برنامج المواهب الجديدة'
            ],
            [
                'description' => 'تكاليف المؤثرات البصرية - مشاهد الأكشن',
                'amount' => 120000,
                'category' => 'مؤثرات بصرية',
                'project_name' => 'فيلم الأكشن الكبير'
            ],
            [
                'description' => 'إيجار استوديو التسجيل - شهر كامل',
                'amount' => 35000,
                'category' => 'إيجارات',
                'project_name' => 'مسلسل الأحلام الذهبية'
            ],
            [
                'description' => 'تكاليف الديكور والأزياء - المشاهد التاريخية',
                'amount' => 65000,
                'category' => 'ديكور وأزياء',
                'project_name' => 'فيلم الأكشن الكبير'
            ]
        ];

        foreach ($pendingExpenses as $expenseData) {
            $project = Project::where('name', $expenseData['project_name'])->first();
            $category = $categories->where('name', $expenseData['category'])->first();
            
            if (!$category) {
                $category = $categories->first();
            }

            if ($project) {
                // الحصول على أول موقع متاح
                $location = \App\Models\Location::first();
                if (!$location) {
                    $location = \App\Models\Location::create([
                        'name' => 'الاستوديو الرئيسي',
                        'address' => 'الرياض، المملكة العربية السعودية'
                    ]);
                }

                // الحصول على أول عنصر مصروف متاح
                $expenseItem = \App\Models\ExpenseItem::first();
                if (!$expenseItem) {
                    $expenseItem = \App\Models\ExpenseItem::create([
                        'name' => 'مصروفات عامة',
                        'expense_category_id' => $category->id
                    ]);
                }

                $expense = Expense::create([
                    'expense_number' => 'EXP-' . time() . '-' . rand(100, 999),
                    'project_id' => $project->id,
                    'location_id' => $location->id,
                    'expense_category_id' => $category->id,
                    'expense_item_id' => $expenseItem->id,
                    'description' => $expenseData['description'],
                    'amount' => $expenseData['amount'],
                    'expense_date' => Carbon::now()->subDays(rand(1, 7)),
                    'status' => 'pending',
                    'created_by' => 1
                ]);

                // إنشاء طلب موافقة
                Approval::create([
                    'approvable_type' => 'App\Models\Expense',
                    'approvable_id' => $expense->id,
                    'user_id' => 1, // المدير المالي
                    'status' => 'pending',
                    'notes' => 'يتطلب موافقة المدير المالي'
                ]);

                echo "  ✓ تم إنشاء مصروف يحتاج موافقة: {$expense->description} ({$expense->amount} ر.س)\n";
            }
        }

        // إنشاء عهد تحتاج موافقة
        $pendingCustodies = [
            [
                'amount' => 25000,
                'purpose' => 'مصروفات التنقل والإقامة - فريق التصوير',
                'project_name' => 'مسلسل الأحلام الذهبية'
            ],
            [
                'amount' => 15000,
                'purpose' => 'شراء مستلزمات الإنتاج الطارئة',
                'project_name' => 'برنامج المواهب الجديدة'
            ],
            [
                'amount' => 40000,
                'purpose' => 'تكاليف المواقع الخارجية - أسبوع التصوير',
                'project_name' => 'فيلم الأكشن الكبير'
            ]
        ];

        foreach ($pendingCustodies as $custodyData) {
            $project = Project::where('name', $custodyData['project_name'])->first();
            
            if ($project) {
                $custody = Custody::create([
                    'custody_number' => 'CUS-' . time() . '-' . rand(100, 999),
                    'project_id' => $project->id,
                    'location_id' => $location->id,
                    'amount' => $custodyData['amount'],
                    'remaining_amount' => $custodyData['amount'],
                    'purpose' => $custodyData['purpose'],
                    'status' => 'requested',
                    'requested_by' => 2, // مساعد الإنتاج
                    'request_date' => Carbon::now()->subDays(rand(1, 5))
                ]);

                // إنشاء طلب موافقة
                Approval::create([
                    'approvable_type' => 'App\Models\Custody',
                    'approvable_id' => $custody->id,
                    'user_id' => 1, // المدير المالي
                    'status' => 'pending',
                    'notes' => 'عهدة تحتاج موافقة المحاسب'
                ]);

                echo "  ✓ تم إنشاء عهدة تحتاج موافقة: {$custody->purpose} ({$custody->amount} ر.س)\n";
            }
        }
    }

    private function createImportantNotifications()
    {
        echo "🔔 إنشاء تنبيهات مهمة...\n";

        $notifications = [
            [
                'title' => 'تحذير: ميزانية مشروع مسلسل الأحلام الذهبية',
                'message' => 'تم استنفاد 95% من ميزانية المشروع. يرجى مراجعة المصروفات فوراً.',
                'level' => 'critical',
                'type' => 'budget_alert'
            ],
            [
                'title' => 'موافقات معلقة تحتاج مراجعة',
                'message' => 'يوجد 8 طلبات موافقة معلقة تحتاج مراجعتك كمدير مالي.',
                'level' => 'warning',
                'type' => 'approval_pending'
            ],
            [
                'title' => 'تجاوز الحد المسموح - فيلم الأكشن الكبير',
                'message' => 'المشروع تجاوز 90% من الميزانية المخصصة. يتطلب تدخل فوري.',
                'level' => 'danger',
                'type' => 'budget_exceeded'
            ],
            [
                'title' => 'عهدة جديدة تحتاج موافقة',
                'message' => 'طلب عهدة بقيمة 40,000 ر.س لمشروع فيلم الأكشن الكبير.',
                'level' => 'info',
                'type' => 'custody_request'
            ],
            [
                'title' => 'تقرير المصروفات الشهرية جاهز',
                'message' => 'تم إنتاج تقرير مصروفات شهر ديسمبر. إجمالي المصروفات: 450,000 ر.س',
                'level' => 'info',
                'type' => 'report_ready'
            ],
            [
                'title' => 'تحديث نظام الموافقات',
                'message' => 'تم تحديث نظام الموافقات الإلكترونية. يرجى مراجعة الطلبات المعلقة.',
                'level' => 'info',
                'type' => 'system_update'
            ]
        ];

        foreach ($notifications as $notificationData) {
            Notification::create([
                'user_id' => 1, // المدير المالي
                'title' => $notificationData['title'],
                'message' => $notificationData['message'],
                'level' => $notificationData['level'],
                'type' => $notificationData['type'],
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(rand(1, 48))
            ]);

            echo "  ✓ تم إنشاء تنبيه: {$notificationData['title']}\n";
        }
    }

    private function createExpenseCategories()
    {
        $categories = [
            'معدات التصوير',
            'أجور الممثلين', 
            'مؤثرات بصرية',
            'إيجارات',
            'ديكور وأزياء',
            'النقل والمواصلات',
            'الإعاشة والضيافة',
            'التسويق والإعلان'
        ];

        foreach ($categories as $categoryName) {
            ExpenseCategory::create([
                'name' => $categoryName,
                'description' => "فئة {$categoryName} للمشاريع الفنية"
            ]);
        }
    }
}
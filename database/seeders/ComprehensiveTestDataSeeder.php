<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Location;
use App\Models\Person;
use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\BudgetAllocation;
use App\Models\Notification;
use App\Models\Approval;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ComprehensiveTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // تنظيف البيانات القديمة
        $this->cleanOldData();
        
        // إنشاء البيانات الجديدة
        $this->createPeople();
        $this->createProjects();
        $this->createLocations(); // بعد المشاريع
        $this->createExpenseCategories();
        $this->createBudgetAllocations();
        $this->createCustodies();
        $this->createExpenses();
        $this->createNotifications();
        $this->createApprovals();
        
        $this->command->info('✅ تم إنشاء بيانات اختبار شاملة بنجاح!');
    }
    
    private function cleanOldData()
    {
        Approval::truncate();
        Notification::truncate();
        Expense::truncate();
        Custody::truncate();
        BudgetAllocation::truncate();
        ExpenseItem::truncate();
        ExpenseCategory::truncate();
        Project::truncate();
        Person::truncate();
        Location::truncate();
    }
    
    private function createLocations()
    {
        $projects = Project::all();
        
        $locations = [
            [
                'project_id' => $projects->first()->id,
                'name' => 'استوديو الرياض الرئيسي',
                'address' => 'حي الملز، طريق الملك فهد، الرياض',
                'city' => 'الرياض',
                'budget_allocated' => 2000000,
                'spent_amount' => 1500000,
                'status' => 'active'
            ],
            [
                'project_id' => $projects->skip(1)->first()->id,
                'name' => 'موقع تصوير جدة التاريخية',
                'address' => 'البلد التاريخية، جدة',
                'city' => 'جدة',
                'budget_allocated' => 800000,
                'spent_amount' => 650000,
                'status' => 'active'
            ],
            [
                'project_id' => $projects->skip(2)->first()->id,
                'name' => 'استوديو الدمام للصوت',
                'address' => 'حي الفيصلية، الدمام',
                'city' => 'الدمام',
                'budget_allocated' => 500000,
                'spent_amount' => 480000,
                'status' => 'active'
            ],
            [
                'project_id' => $projects->skip(3)->first()->id,
                'name' => 'موقع تصوير صحراء النفود',
                'address' => 'صحراء النفود الكبير، منطقة حائل',
                'city' => 'حائل',
                'budget_allocated' => 1200000,
                'spent_amount' => 300000,
                'status' => 'active'
            ],
            [
                'project_id' => $projects->skip(4)->first()->id,
                'name' => 'استوديو أبها الجبلي',
                'address' => 'منطقة السودة، أبها',
                'city' => 'أبها',
                'budget_allocated' => 600000,
                'spent_amount' => 150000,
                'status' => 'active'
            ]
        ];
        
        foreach ($locations as $location) {
            Location::create($location);
        }
    }
    
    private function createPeople()
    {
        $people = [
            ['name' => 'أحمد محمد الشمري', 'type' => 'crew', 'phone' => '0501234567', 'id_number' => '1234567890'],
            ['name' => 'فاطمة علي القحطاني', 'type' => 'crew', 'phone' => '0502345678', 'id_number' => '1234567891'],
            ['name' => 'محمد سعد العنزي', 'type' => 'technician', 'phone' => '0503456789', 'id_number' => '1234567892'],
            ['name' => 'نورا خالد الدوسري', 'type' => 'actor', 'phone' => '0504567890', 'id_number' => '1234567893'],
            ['name' => 'عبدالرحمن أحمد المطيري', 'type' => 'technician', 'phone' => '0505678901', 'id_number' => '1234567894'],
            ['name' => 'سارة محمد الغامدي', 'type' => 'crew', 'phone' => '0506789012', 'id_number' => '1234567895'],
            ['name' => 'خالد عبدالله الحربي', 'type' => 'technician', 'phone' => '0507890123', 'id_number' => '1234567896'],
            ['name' => 'منى سليمان الزهراني', 'type' => 'crew', 'phone' => '0508901234', 'id_number' => '1234567897'],
            ['name' => 'عمر فهد البقمي', 'type' => 'technician', 'phone' => '0509012345', 'id_number' => '1234567898'],
            ['name' => 'ريم عبدالعزيز السديري', 'type' => 'crew', 'phone' => '0500123456', 'id_number' => '1234567899']
        ];
        
        foreach ($people as $person) {
            Person::create($person);
        }
    }
    
    private function createProjects()
    {
        $projects = [
            [
                'name' => 'مسلسل وطن الأحلام',
                'description' => 'مسلسل درامي اجتماعي يحكي قصة عائلة سعودية عبر ثلاثة أجيال',
                'type' => 'series',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(2),
                'total_budget' => 8500000,
                'spent_amount' => 6200000,
                'status' => 'active',
                'episodes_count' => 30
            ],
            [
                'name' => 'برنامج حديث المملكة',
                'description' => 'برنامج حواري أسبوعي يناقش القضايا المجتمعية والثقافية',
                'type' => 'program',
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(6),
                'total_budget' => 4200000,
                'spent_amount' => 2800000,
                'status' => 'active',
                'episodes_count' => 52
            ],
            [
                'name' => 'فيلم رحلة في التاريخ',
                'description' => 'فيلم وثائقي عن تاريخ المملكة العربية السعودية',
                'type' => 'movie',
                'start_date' => Carbon::now()->subMonths(4),
                'end_date' => Carbon::now()->addMonth(),
                'total_budget' => 2800000,
                'spent_amount' => 2650000,
                'status' => 'active',
                'episodes_count' => 1
            ],
            [
                'name' => 'مسلسل أساطير الصحراء',
                'description' => 'مسلسل تاريخي يحكي قصص البطولة في الجزيرة العربية',
                'type' => 'series',
                'start_date' => Carbon::now()->subMonth(),
                'end_date' => Carbon::now()->addMonths(4),
                'total_budget' => 12000000,
                'spent_amount' => 3600000,
                'status' => 'active',
                'episodes_count' => 25
            ],
            [
                'name' => 'برنامج جبال وطني',
                'description' => 'برنامج سياحي يستكشف المناطق الجبلية في المملكة',
                'type' => 'program',
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addMonths(3),
                'total_budget' => 1800000,
                'spent_amount' => 450000,
                'status' => 'active',
                'episodes_count' => 12
            ],
            [
                'name' => 'مسلسل حكايات المدينة',
                'description' => 'مسلسل كوميدي اجتماعي عن الحياة في المدن السعودية',
                'type' => 'series',
                'start_date' => Carbon::now()->subMonths(5),
                'end_date' => Carbon::now()->subMonth(),
                'total_budget' => 5500000,
                'spent_amount' => 5500000,
                'status' => 'completed',
                'episodes_count' => 20
            ]
        ];
        
        foreach ($projects as $project) {
            Project::create($project);
        }
    }
    
    private function createExpenseCategories()
    {
        $categories = [
            ['code' => 1001, 'name' => 'أجور الممثلين', 'color' => '#3B82F6', 'description' => 'رواتب ومكافآت الممثلين الرئيسيين والثانويين'],
            ['code' => 1002, 'name' => 'أجور طاقم العمل', 'color' => '#10B981', 'description' => 'رواتب المخرجين والفنيين وطاقم الإنتاج'],
            ['code' => 1003, 'name' => 'معدات التصوير', 'color' => '#F59E0B', 'description' => 'إيجار وشراء كاميرات ومعدات التصوير'],
            ['code' => 1004, 'name' => 'الديكور والأزياء', 'color' => '#EF4444', 'description' => 'تكاليف الديكور وتصميم الأزياء والمكياج'],
            ['code' => 1005, 'name' => 'المواصلات والسفر', 'color' => '#8B5CF6', 'description' => 'تكاليف النقل والسفر للمواقع'],
            ['code' => 1006, 'name' => 'الطعام والضيافة', 'color' => '#06B6D4', 'description' => 'وجبات الطعام والضيافة لفريق العمل'],
            ['code' => 1007, 'name' => 'الإضاءة والصوت', 'color' => '#84CC16', 'description' => 'معدات الإضاءة وتسجيل الصوت'],
            ['code' => 1008, 'name' => 'المونتاج والمؤثرات', 'color' => '#F97316', 'description' => 'تكاليف المونتاج والمؤثرات البصرية'],
            ['code' => 1009, 'name' => 'التسويق والدعاية', 'color' => '#EC4899', 'description' => 'حملات التسويق والإعلان'],
            ['code' => 1010, 'name' => 'مصروفات إدارية', 'color' => '#6B7280', 'description' => 'المصروفات الإدارية والقانونية']
        ];
        
        foreach ($categories as $categoryData) {
            $category = ExpenseCategory::create($categoryData);
            
            // إضافة بنود للفئة
            $this->createExpenseItems($category);
        }
    }
    
    private function createExpenseItems($category)
    {
        $items = [
            'أجور الممثلين' => ['راتب الممثل الرئيسي', 'راتب الممثلة الرئيسية', 'أجور الممثلين الثانويين', 'مكافآت الأداء'],
            'أجور طاقم العمل' => ['راتب المخرج', 'راتب مدير التصوير', 'أجور الفنيين', 'راتب المنتج التنفيذي'],
            'معدات التصوير' => ['إيجار كاميرات', 'عدسات التصوير', 'حوامل الكاميرات', 'بطاريات ومعدات'],
            'الديكور والأزياء' => ['مواد الديكور', 'تصميم الأزياء', 'أدوات المكياج', 'إكسسوارات'],
            'المواصلات والسفر' => ['تذاكر الطيران', 'إيجار السيارات', 'الوقود', 'الإقامة'],
            'الطعام والضيافة' => ['وجبات الإفطار', 'وجبات الغداء', 'وجبات العشاء', 'المشروبات والحلويات'],
            'الإضاءة والصوت' => ['معدات الإضاءة', 'أجهزة التسجيل', 'الميكروفونات', 'كابلات الصوت'],
            'المونتاج والمؤثرات' => ['برامج المونتاج', 'المؤثرات البصرية', 'الموسيقى التصويرية', 'التلوين'],
            'التسويق والدعاية' => ['إعلانات تلفزيونية', 'حملات وسائل التواصل', 'ملصقات ومطبوعات', 'فعاليات ترويجية'],
            'مصروفات إدارية' => ['رسوم قانونية', 'تأمينات', 'اتصالات', 'مصروفات مكتبية']
        ];
        
        if (isset($items[$category->name])) {
            $codeCounter = 1;
            foreach ($items[$category->name] as $itemName) {
                ExpenseItem::create([
                    'code' => ($category->code * 100) + $codeCounter,
                    'name' => $itemName,
                    'expense_category_id' => $category->id,
                    'requires_invoice' => true,
                    'approval_level' => 'automatic'
                ]);
                $codeCounter++;
            }
        }
    }
    
    private function createBudgetAllocations()
    {
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        
        foreach ($projects as $project) {
            $totalBudget = $project->total_budget;
            $allocations = [
                'أجور الممثلين' => 0.35,
                'أجور طاقم العمل' => 0.25,
                'معدات التصوير' => 0.15,
                'الديكور والأزياء' => 0.10,
                'المواصلات والسفر' => 0.05,
                'الطعام والضيافة' => 0.03,
                'الإضاءة والصوت' => 0.03,
                'المونتاج والمؤثرات' => 0.02,
                'التسويق والدعاية' => 0.01,
                'مصروفات إدارية' => 0.01
            ];
            
            foreach ($categories as $category) {
                if (isset($allocations[$category->name])) {
                    BudgetAllocation::create([
                        'project_id' => $project->id,
                        'expense_category_id' => $category->id,
                        'allocated_amount' => $totalBudget * $allocations[$category->name],
                        'spent_amount' => 0,
                        'percentage' => $allocations[$category->name] * 100
                    ]);
                }
            }
        }
    }
    
    private function createCustodies()
    {
        $projects = Project::where('status', 'active')->get();
        $users = \App\Models\User::all();
        
        $custodyData = [
            ['amount' => 150000, 'purpose' => 'مصروفات تصوير الأسبوع الأول', 'status' => 'active'],
            ['amount' => 85000, 'purpose' => 'شراء معدات إضاءة جديدة', 'status' => 'requested'],
            ['amount' => 120000, 'purpose' => 'أجور الممثلين الضيوف', 'status' => 'active'],
            ['amount' => 65000, 'purpose' => 'مصروفات السفر لموقع التصوير', 'status' => 'settled'],
            ['amount' => 200000, 'purpose' => 'ميزانية طوارئ للإنتاج', 'status' => 'requested'],
            ['amount' => 95000, 'purpose' => 'تكاليف الديكور والأزياء', 'status' => 'active'],
            ['amount' => 75000, 'purpose' => 'مصروفات الطعام والضيافة', 'status' => 'settled'],
            ['amount' => 110000, 'purpose' => 'إيجار معدات التصوير المتخصصة', 'status' => 'active']
        ];
        
        foreach ($custodyData as $index => $data) {
            Custody::create([
                'custody_number' => 'CUST-2025-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'project_id' => $projects->random()->id,
                'requested_by' => $users->random()->id,
                'amount' => $data['amount'],
                'purpose' => $data['purpose'],
                'status' => $data['status'],
                'request_date' => Carbon::now()->subDays(rand(1, 30)),
                'approved_by' => $data['status'] !== 'requested' ? $users->where('role', 'financial_manager')->first()->id : null,
                'approved_at' => $data['status'] !== 'requested' ? Carbon::now()->subDays(rand(1, 25)) : null,
                'notes' => 'عهدة للمشروع - ' . $data['purpose']
            ]);
        }
    }
    
    private function createExpenses()
    {
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $items = ExpenseItem::all();
        $people = Person::all();
        $users = \App\Models\User::all();
        
        // إنشاء 200 مصروف متنوع
        for ($i = 0; $i < 200; $i++) {
            $project = $projects->random();
            $category = $categories->random();
            $categoryItems = $items->where('expense_category_id', $category->id);
            $item = $categoryItems->isNotEmpty() ? $categoryItems->random() : $items->random();
            
            $amounts = [
                'أجور الممثلين' => [50000, 150000],
                'أجور طاقم العمل' => [15000, 80000],
                'معدات التصوير' => [5000, 45000],
                'الديكور والأزياء' => [2000, 25000],
                'المواصلات والسفر' => [1000, 15000],
                'الطعام والضيافة' => [500, 5000],
                'الإضاءة والصوت' => [3000, 20000],
                'المونتاج والمؤثرات' => [8000, 35000],
                'التسويق والدعاية' => [10000, 50000],
                'مصروفات إدارية' => [1000, 10000]
            ];
            
            $range = $amounts[$category->name] ?? [1000, 10000];
            $amount = rand($range[0], $range[1]);
            
            $statuses = ['pending', 'approved', 'rejected'];
            $status = $statuses[array_rand($statuses)];
            
            Expense::create([
                'project_id' => $project->id,
                'expense_category_id' => $category->id,
                'expense_item_id' => $item->id,
                'person_id' => $people->random()->id,
                'user_id' => $users->random()->id,
                'amount' => $amount,
                'description' => "مصروف {$item->name} لمشروع {$project->name}",
                'expense_date' => Carbon::now()->subDays(rand(1, 90)),
                'status' => $status,
                'approved_by' => $status === 'approved' ? $users->where('role', 'financial_manager')->first()->id : null,
                'approved_at' => $status === 'approved' ? Carbon::now()->subDays(rand(1, 30)) : null,
                'receipt_number' => 'REC-' . date('Y') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'notes' => 'مصروف تم إنشاؤه للاختبار'
            ]);
        }
        
        // تحديث المبالغ المصروفة في المشاريع
        foreach ($projects as $project) {
            $totalSpent = Expense::where('project_id', $project->id)
                ->where('status', 'approved')
                ->sum('amount');
            
            $project->update(['spent_amount' => $totalSpent]);
        }
    }
    
    private function createNotifications()
    {
        $users = \App\Models\User::all();
        $projects = Project::all();
        
        $notifications = [
            [
                'title' => 'تجاوز في الميزانية',
                'message' => 'مشروع "وطن الأحلام" تجاوز 90% من الميزانية المخصصة',
                'level' => 'critical',
                'type' => 'budget_alert'
            ],
            [
                'title' => 'عهدة تحتاج موافقة',
                'message' => 'عهدة جديدة بمبلغ 150,000 ريال تحتاج موافقتك',
                'level' => 'warning',
                'type' => 'custody_approval'
            ],
            [
                'title' => 'مصروف مرفوض',
                'message' => 'تم رفض مصروف بمبلغ 25,000 ريال لعدم وجود فاتورة',
                'level' => 'danger',
                'type' => 'expense_rejected'
            ],
            [
                'title' => 'اكتمال مشروع',
                'message' => 'تم اكتمال مشروع "حكايات المدينة" بنجاح',
                'level' => 'success',
                'type' => 'project_completed'
            ],
            [
                'title' => 'تذكير موعد تسليم',
                'message' => 'موعد تسليم مشروع "رحلة في التاريخ" خلال أسبوع',
                'level' => 'info',
                'type' => 'deadline_reminder'
            ]
        ];
        
        foreach ($users as $user) {
            foreach ($notifications as $index => $notificationData) {
                if ($index < 3 || $user->role === 'financial_manager') {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'title' => $notificationData['title'],
                        'message' => $notificationData['message'],
                        'level' => $notificationData['level'],
                        'type' => $notificationData['type'],
                        'is_read' => rand(0, 1) === 1,
                        'created_at' => Carbon::now()->subHours(rand(1, 72))
                    ]);
                }
            }
        }
    }
    
    private function createApprovals()
    {
        $custodies = Custody::where('status', 'requested')->get();
        $expenses = Expense::where('status', 'pending')->take(10)->get();
        $users = \App\Models\User::all();
        
        foreach ($custodies as $custody) {
            Approval::create([
                'approvable_type' => 'App\\Models\\Custody',
                'approvable_id' => $custody->id,
                'user_id' => $users->where('role', 'financial_manager')->first()->id,
                'status' => 'pending',
                'notes' => 'في انتظار موافقة المدير المالي',
                'created_at' => $custody->request_date
            ]);
        }
        
        foreach ($expenses as $expense) {
            Approval::create([
                'approvable_type' => 'App\\Models\\Expense',
                'approvable_id' => $expense->id,
                'user_id' => $users->where('role', 'financial_manager')->first()->id,
                'status' => 'pending',
                'notes' => 'مصروف يحتاج موافقة مالية',
                'created_at' => $expense->created_at
            ]);
        }
    }
}
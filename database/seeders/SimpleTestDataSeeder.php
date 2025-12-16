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
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SimpleTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 إنشاء بيانات اختبار بسيطة...');
        
        // إنشاء الأشخاص
        $this->createPeople();
        
        // إنشاء المشاريع
        $this->createProjects();
        
        // إنشاء المواقع
        $this->createLocations();
        
        // إنشاء فئات المصروفات
        $this->createExpenseCategories();
        
        // إنشاء تخصيصات الميزانية
        $this->createBudgetAllocations();
        
        // إنشاء العهد
        $this->createCustodies();
        
        // إنشاء المصروفات
        $this->createExpenses();
        
        $this->command->info('✅ تم إنشاء البيانات بنجاح!');
    }
    
    private function createPeople()
    {
        $people = [
            ['name' => 'أحمد محمد الشمري', 'type' => 'crew', 'phone' => '0501234567', 'id_number' => '1234567890'],
            ['name' => 'فاطمة علي القحطاني', 'type' => 'crew', 'phone' => '0502345678', 'id_number' => '1234567891'],
            ['name' => 'محمد سعد العنزي', 'type' => 'technician', 'phone' => '0503456789', 'id_number' => '1234567892'],
            ['name' => 'نورا خالد الدوسري', 'type' => 'actor', 'phone' => '0504567890', 'id_number' => '1234567893'],
            ['name' => 'عبدالرحمن أحمد المطيري', 'type' => 'technician', 'phone' => '0505678901', 'id_number' => '1234567894'],
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
                'spent_amount' => 7650000, // 90% - حرج
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
                'spent_amount' => 2800000, // 67% - آمن
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
                'spent_amount' => 2520000, // 90% - حرج
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
                'spent_amount' => 3600000, // 30% - آمن
                'status' => 'active',
                'episodes_count' => 25
            ]
        ];
        
        foreach ($projects as $project) {
            Project::create($project);
        }
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
                'spent_amount' => 1800000,
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
            ]
        ];
        
        foreach ($locations as $location) {
            Location::create($location);
        }
    }
    
    private function createExpenseCategories()
    {
        $categories = [
            ['code' => 1001, 'name' => 'أجور الممثلين', 'color' => '#3B82F6'],
            ['code' => 1002, 'name' => 'أجور طاقم العمل', 'color' => '#10B981'],
            ['code' => 1003, 'name' => 'معدات التصوير', 'color' => '#F59E0B'],
            ['code' => 1004, 'name' => 'الديكور والأزياء', 'color' => '#EF4444'],
            ['code' => 1005, 'name' => 'المواصلات والسفر', 'color' => '#8B5CF6'],
        ];
        
        foreach ($categories as $categoryData) {
            $category = ExpenseCategory::create($categoryData);
            
            // إضافة بنود للفئة
            for ($i = 1; $i <= 3; $i++) {
                ExpenseItem::create([
                    'code' => ($category->code * 100) + $i,
                    'name' => "بند {$i} - {$category->name}",
                    'expense_category_id' => $category->id,
                    'requires_invoice' => true,
                    'approval_level' => 'automatic'
                ]);
            }
        }
    }
    
    private function createBudgetAllocations()
    {
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        
        foreach ($projects as $project) {
            $totalBudget = $project->total_budget;
            $percentages = [35, 25, 20, 15, 5]; // نسب توزيع الميزانية
            
            foreach ($categories as $index => $category) {
                $percentage = $percentages[$index] ?? 5;
                BudgetAllocation::create([
                    'project_id' => $project->id,
                    'expense_category_id' => $category->id,
                    'allocated_amount' => $totalBudget * ($percentage / 100),
                    'spent_amount' => 0,
                    'percentage' => $percentage
                ]);
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
        ];
        
        $locations = Location::all();
        
        foreach ($custodyData as $index => $data) {
            Custody::create([
                'custody_number' => 'CUST-2025-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'project_id' => $projects->random()->id,
                'location_id' => $locations->isNotEmpty() ? $locations->random()->id : 1,
                'requested_by' => $users->random()->id,
                'amount' => $data['amount'],
                'remaining_amount' => $data['amount'],
                'purpose' => $data['purpose'],
                'status' => $data['status'],
                'request_date' => Carbon::now()->subDays(rand(1, 30))
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
        
        // إنشاء مصروفات متنوعة للمخططات البيانية
        $this->createExpensesForCharts($projects, $categories, $items, $people, $locations);
        
        // تحديث المبالغ المصروفة في المشاريع
        foreach ($projects as $project) {
            $totalSpent = Expense::where('project_id', $project->id)
                ->where('status', 'approved')
                ->sum('amount');
            
            $project->update(['spent_amount' => $project->spent_amount + $totalSpent]);
        }
    }
    
    private function createExpensesForCharts($projects, $categories, $items, $people, $locations)
    {
        // مصروفات شهرية متنوعة للعام الحالي
        $monthlyAmounts = [
            '01' => [450000, 380000, 520000], // يناير
            '02' => [620000, 480000, 390000], // فبراير
            '03' => [580000, 720000, 450000], // مارس
            '04' => [750000, 680000, 590000], // أبريل
            '05' => [420000, 350000, 480000], // مايو
            '06' => [680000, 590000, 720000], // يونيو
            '07' => [520000, 480000, 380000], // يوليو
            '08' => [590000, 650000, 420000], // أغسطس
            '09' => [480000, 520000, 680000], // سبتمبر
            '10' => [720000, 580000, 450000], // أكتوبر
            '11' => [380000, 420000, 590000], // نوفمبر
            '12' => [650000, 720000, 480000]  // ديسمبر
        ];
        
        $expenseNumber = 1;
        
        // إنشاء مصروفات لكل شهر
        foreach ($monthlyAmounts as $month => $amounts) {
            foreach ($amounts as $dayIndex => $amount) {
                $project = $projects->random();
                $category = $categories->random();
                $categoryItems = $items->where('expense_category_id', $category->id);
                $item = $categoryItems->isNotEmpty() ? $categoryItems->random() : $items->random();
                
                $expenseDate = Carbon::create(date('Y'), intval($month), rand(1, 28));
                
                Expense::create([
                    'expense_number' => 'EXP-' . date('Y') . '-' . str_pad($expenseNumber++, 4, '0', STR_PAD_LEFT),
                    'project_id' => $project->id,
                    'location_id' => $locations->isNotEmpty() ? $locations->random()->id : 1,
                    'expense_category_id' => $category->id,
                    'expense_item_id' => $item->id,
                    'person_id' => $people->random()->id,
                    'amount' => $amount,
                    'description' => "مصروف {$item->name} لمشروع {$project->name}",
                    'expense_date' => $expenseDate,
                    'status' => 'approved'
                ]);
            }
        }
        
        // إضافة مصروفات إضافية لتوزيع أفضل على الفئات
        $categoryDistribution = [
            'أجور الممثلين' => [850000, 920000, 780000, 1100000],
            'أجور طاقم العمل' => [650000, 720000, 580000, 890000],
            'معدات التصوير' => [480000, 520000, 680000, 750000],
            'الديكور والأزياء' => [380000, 450000, 520000, 620000],
            'المواصلات والسفر' => [280000, 320000, 380000, 450000]
        ];
        
        foreach ($categoryDistribution as $categoryName => $amounts) {
            $category = $categories->where('name', $categoryName)->first();
            if ($category) {
                foreach ($amounts as $amount) {
                    $project = $projects->random();
                    $categoryItems = $items->where('expense_category_id', $category->id);
                    $item = $categoryItems->isNotEmpty() ? $categoryItems->random() : $items->random();
                    
                    Expense::create([
                        'expense_number' => 'EXP-' . date('Y') . '-' . str_pad($expenseNumber++, 4, '0', STR_PAD_LEFT),
                        'project_id' => $project->id,
                        'location_id' => $locations->isNotEmpty() ? $locations->random()->id : 1,
                        'expense_category_id' => $category->id,
                        'expense_item_id' => $item->id,
                        'person_id' => $people->random()->id,
                        'amount' => $amount,
                        'description' => "مصروف {$item->name} لمشروع {$project->name}",
                        'expense_date' => Carbon::now()->subDays(rand(1, 90)),
                        'status' => 'approved'
                    ]);
                }
            }
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Location;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\BudgetAllocation;
use App\Models\Person;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@pems.com')->first();

        // مستخدمون
        $users = [
            ['name' => 'أحمد المالكي', 'email' => 'ahmed.malki@pems.com', 'password' => Hash::make('password123'), 'role' => 'admin_accountant', 'location' => 'الرياض'],
            ['name' => 'فاطمة العتيبي', 'email' => 'fatima.otaibi@pems.com', 'password' => Hash::make('password123'), 'role' => 'production_manager', 'location' => 'جدة'],
        ];
        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // أشخاص
        $people = [
            ['name' => 'محمد الغامدي', 'type' => 'actor', 'phone' => '0501234567', 'id_number' => '1023456789'],
            ['name' => 'نورة الحربي', 'type' => 'actor', 'phone' => '0509876543', 'id_number' => '1034567890'],
            ['name' => 'سعد القحطاني', 'type' => 'actor', 'phone' => '0507654321', 'id_number' => '1045678901'],
            ['name' => 'ريم الدوسري', 'type' => 'actor', 'phone' => '0503456789', 'id_number' => '1056789012'],
            ['name' => 'عبدالعزيز الشهري', 'type' => 'technician', 'phone' => '0502345678', 'id_number' => '1067890123'],
            ['name' => 'هند المطيري', 'type' => 'technician', 'phone' => '0508765432', 'id_number' => '1078901234'],
        ];
        foreach ($people as $person) {
            Person::firstOrCreate(['id_number' => $person['id_number']], $person);
        }

        // مشاريع
        $projects = [
            ['name' => 'مسلسل عائلة الوادي', 'type' => 'series', 'description' => 'مسلسل درامي اجتماعي 30 حلقة', 'start_date' => '2025-01-15', 'end_date' => '2025-04-15', 'total_budget' => 8500000, 'episodes_count' => 30, 'planned_days' => 90, 'emergency_reserve' => 850000, 'status' => 'active'],
            ['name' => 'برنامج صباح الخير', 'type' => 'program', 'description' => 'برنامج صباحي يومي', 'start_date' => '2025-02-01', 'end_date' => '2025-12-31', 'total_budget' => 4200000, 'episodes_count' => 220, 'planned_days' => 220, 'emergency_reserve' => 420000, 'status' => 'active'],
            ['name' => 'فيلم رحلة العمر', 'type' => 'movie', 'description' => 'فيلم سينمائي', 'start_date' => '2025-03-01', 'end_date' => '2025-07-30', 'total_budget' => 6000000, 'planned_days' => 60, 'emergency_reserve' => 600000, 'status' => 'planning'],
            ['name' => 'مسلسل أسرار المدينة', 'type' => 'series', 'description' => 'مسلسل تشويقي 15 حلقة', 'start_date' => '2025-02-15', 'end_date' => '2025-05-15', 'total_budget' => 5500000, 'episodes_count' => 15, 'planned_days' => 60, 'emergency_reserve' => 550000, 'status' => 'active'],
        ];
        $projectIds = [];
        foreach ($projects as $proj) {
            $project = Project::firstOrCreate(['name' => $proj['name']], $proj);
            $projectIds[] = $project->id;
        }

        // مواقع
        $locations = [
            ['project_id' => $projectIds[0], 'name' => 'استوديو الرياض', 'city' => 'الرياض', 'address' => 'حي النخيل', 'budget_allocated' => 3000000, 'status' => 'active'],
            ['project_id' => $projectIds[0], 'name' => 'موقع الدرعية', 'city' => 'الرياض', 'address' => 'حي الطريف', 'budget_allocated' => 1500000, 'status' => 'active'],
            ['project_id' => $projectIds[1], 'name' => 'استوديو جدة', 'city' => 'جدة', 'address' => 'الكورنيش', 'budget_allocated' => 2000000, 'status' => 'active'],
            ['project_id' => $projectIds[3], 'name' => 'استوديو الدمام', 'city' => 'الدمام', 'address' => 'حي الفيصلية', 'budget_allocated' => 2000000, 'status' => 'active'],
        ];
        $locationIds = [];
        foreach ($locations as $loc) {
            $location = Location::firstOrCreate(['project_id' => $loc['project_id'], 'name' => $loc['name']], $loc);
            $locationIds[] = $location->id;
        }

        // ميزانيات
        $budgets = [
            ['project_id' => $projectIds[0], 'expense_category_id' => 1, 'allocated_amount' => 2500000, 'spent_amount' => 850000, 'percentage' => 29.41],
            ['project_id' => $projectIds[0], 'expense_category_id' => 2, 'allocated_amount' => 1800000, 'spent_amount' => 620000, 'percentage' => 21.18],
            ['project_id' => $projectIds[1], 'expense_category_id' => 1, 'allocated_amount' => 1500000, 'spent_amount' => 420000, 'percentage' => 35.71],
            ['project_id' => $projectIds[3], 'expense_category_id' => 1, 'allocated_amount' => 2000000, 'spent_amount' => 550000, 'percentage' => 36.36],
        ];
        foreach ($budgets as $budget) {
            BudgetAllocation::firstOrCreate($budget);
        }

        // عهد
        $custodies = [
            ['custody_number' => 'CUST-2025-101', 'project_id' => $projectIds[0], 'location_id' => $locationIds[0], 'requested_by' => $admin->id, 'approved_by' => $admin->id, 'amount' => 150000, 'spent_amount' => 85000, 'remaining_amount' => 65000, 'purpose' => 'مصاريف تصوير الحلقات 1-5', 'request_date' => '2025-01-15', 'approval_date' => '2025-01-16', 'received_date' => '2025-01-17', 'due_date' => '2025-02-15', 'status' => 'active'],
            ['custody_number' => 'CUST-2025-102', 'project_id' => $projectIds[0], 'location_id' => $locationIds[1], 'requested_by' => $admin->id, 'approved_by' => $admin->id, 'amount' => 80000, 'spent_amount' => 45000, 'remaining_amount' => 35000, 'purpose' => 'تصوير المشاهد الخارجية', 'request_date' => '2025-01-20', 'approval_date' => '2025-01-21', 'received_date' => '2025-01-22', 'due_date' => '2025-02-20', 'status' => 'active'],
            ['custody_number' => 'CUST-2025-103', 'project_id' => $projectIds[1], 'location_id' => $locationIds[2], 'requested_by' => $admin->id, 'approved_by' => $admin->id, 'amount' => 60000, 'spent_amount' => 60000, 'remaining_amount' => 0, 'purpose' => 'مصاريف البرنامج - فبراير', 'request_date' => '2025-02-01', 'approval_date' => '2025-02-01', 'received_date' => '2025-02-02', 'due_date' => '2025-03-01', 'status' => 'under_settlement'],
        ];
        $custodyIds = [];
        foreach ($custodies as $custody) {
            $cust = Custody::firstOrCreate(['custody_number' => $custody['custody_number']], $custody);
            $custodyIds[] = $cust->id;
        }

        $personIds = Person::pluck('id')->toArray();

        // مصروفات
        $expenses = [
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[0], 'expense_category_id' => 1, 'expense_item_id' => 1, 'custody_id' => $custodyIds[0], 'person_id' => $personIds[0], 'description' => 'أجور محمد الغامدي - 5 حلقات', 'amount' => 250000, 'expense_date' => '2025-01-20', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[0], 'expense_category_id' => 1, 'expense_item_id' => 1, 'custody_id' => $custodyIds[0], 'person_id' => $personIds[1], 'description' => 'أجور نورة الحربي - 5 حلقات', 'amount' => 180000, 'expense_date' => '2025-01-22', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[0], 'expense_category_id' => 2, 'expense_item_id' => 8, 'custody_id' => $custodyIds[0], 'description' => 'إيجار كاميرات RED KOMODO', 'amount' => 85000, 'expense_date' => '2025-01-18', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[0], 'expense_category_id' => 2, 'expense_item_id' => 9, 'custody_id' => $custodyIds[0], 'description' => 'معدات إضاءة', 'amount' => 45000, 'expense_date' => '2025-01-19', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[1], 'expense_category_id' => 3, 'expense_item_id' => 15, 'custody_id' => $custodyIds[1], 'description' => 'ديكور منزل العائلة', 'amount' => 180000, 'expense_date' => '2025-01-23', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[1], 'expense_category_id' => 3, 'expense_item_id' => 16, 'custody_id' => $custodyIds[1], 'description' => 'أزياء الممثلين', 'amount' => 65000, 'expense_date' => '2025-01-24', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[0], 'expense_category_id' => 4, 'expense_item_id' => 22, 'description' => 'وجبات فريق العمل', 'amount' => 18000, 'expense_date' => '2025-01-27', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[0], 'location_id' => $locationIds[0], 'expense_category_id' => 5, 'expense_item_id' => 28, 'description' => 'تذاكر طيران', 'amount' => 24000, 'expense_date' => '2025-01-29', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[1], 'location_id' => $locationIds[2], 'expense_category_id' => 1, 'expense_item_id' => 1, 'custody_id' => $custodyIds[2], 'description' => 'أجور مقدم البرنامج', 'amount' => 120000, 'expense_date' => '2025-02-05', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[1], 'location_id' => $locationIds[2], 'expense_category_id' => 3, 'expense_item_id' => 15, 'custody_id' => $custodyIds[2], 'description' => 'تجديد ديكور الاستوديو', 'amount' => 85000, 'expense_date' => '2025-02-12', 'status' => 'approved', 'created_by' => $admin->id, 'approved_by' => $admin->id],
            ['project_id' => $projectIds[3], 'location_id' => $locationIds[3], 'expense_category_id' => 1, 'expense_item_id' => 1, 'person_id' => $personIds[3], 'description' => 'أجور ريم الدوسري', 'amount' => 150000, 'expense_date' => '2025-02-18', 'status' => 'pending', 'created_by' => $admin->id],
            ['project_id' => $projectIds[3], 'location_id' => $locationIds[3], 'expense_category_id' => 2, 'expense_item_id' => 8, 'description' => 'معدات تصوير متخصصة', 'amount' => 95000, 'expense_date' => '2025-02-20', 'status' => 'pending', 'created_by' => $admin->id],
        ];

        foreach ($expenses as $index => $expense) {
            $expense['expense_number'] = 'EXP-2025-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            Expense::create($expense);
        }

        echo "\n✅ تم إضافة بيانات واقعية!\n";
        echo "📊 المشاريع: 4\n";
        echo "📍 المواقع: 4\n";
        echo "👥 الأشخاص: 6\n";
        echo "💰 العهد: 3\n";
        echo "💵 المصروفات: 12\n\n";
    }
}

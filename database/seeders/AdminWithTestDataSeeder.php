<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Location;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\BudgetAllocation;
use App\Models\Person;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminWithTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء حساب المدير المالي (أعلى صلاحية)
        $admin = User::firstOrCreate(
            ['email' => 'admin@pems.com'],
            [
                'name' => 'المدير العام',
                'password' => Hash::make('admin123'),
                'role' => 'financial_manager',
                'location' => 'المقر الرئيسي'
            ]
        );

        // إنشاء أشخاص
        $people = [
            ['name' => 'عبدالله الشمري', 'type' => 'crew', 'phone' => '0501234567', 'id_number' => '1234567890'],
            ['name' => 'سارة القحطاني', 'type' => 'technician', 'phone' => '0507654321', 'id_number' => '1234567891'],
            ['name' => 'فهد العنزي', 'type' => 'technician', 'phone' => '0509876543', 'id_number' => '1234567892'],
            ['name' => 'نورة الدوسري', 'type' => 'actor', 'phone' => '0503456789', 'id_number' => '1234567893'],
        ];

        foreach ($people as $person) {
            Person::firstOrCreate(['id_number' => $person['id_number']], $person);
        }

        // إنشاء مشاريع
        $projects = [
            [
                'name' => 'مسلسل رمضان 2025',
                'type' => 'series',
                'description' => 'مسلسل درامي اجتماعي 30 حلقة',
                'start_date' => '2025-01-01',
                'end_date' => '2025-03-30',
                'total_budget' => 5000000,
                'episodes_count' => 30,
                'planned_days' => 90,
                'status' => 'active'
            ],
            [
                'name' => 'برنامج توك شو أسبوعي',
                'type' => 'program',
                'description' => 'برنامج حواري يستضيف شخصيات مؤثرة',
                'start_date' => '2025-02-01',
                'end_date' => '2025-12-31',
                'total_budget' => 2500000,
                'episodes_count' => 48,
                'planned_days' => 240,
                'status' => 'active'
            ],
            [
                'name' => 'فيلم وثائقي - تراث السعودية',
                'type' => 'movie',
                'description' => 'فيلم وثائقي عن التراث السعودي',
                'start_date' => '2025-01-15',
                'end_date' => '2025-06-30',
                'total_budget' => 1500000,
                'planned_days' => 60,
                'status' => 'active'
            ],
        ];

        $projectIds = [];
        foreach ($projects as $proj) {
            $project = Project::firstOrCreate(['name' => $proj['name']], $proj);
            $projectIds[] = $project->id;
        }

        // إنشاء مواقع للمشاريع
        $locations = [
            ['project_id' => $projectIds[0], 'name' => 'الرياض - الاستوديو الرئيسي', 'city' => 'الرياض', 'address' => 'حي النخيل', 'budget_allocated' => 2000000],
            ['project_id' => $projectIds[1], 'name' => 'جدة - موقع التصوير', 'city' => 'جدة', 'address' => 'كورنيش جدة', 'budget_allocated' => 1000000],
            ['project_id' => $projectIds[2], 'name' => 'الدمام - موقع التصوير', 'city' => 'الدمام', 'address' => 'حي الفيصلية', 'budget_allocated' => 500000],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(['project_id' => $loc['project_id'], 'name' => $loc['name']], $loc);
        }

        // إنشاف ميزانيات
        $budgets = [
            ['project_id' => $projectIds[0], 'expense_category_id' => 1, 'allocated_amount' => 1500000, 'percentage' => 30.00],
            ['project_id' => $projectIds[0], 'expense_category_id' => 2, 'allocated_amount' => 800000, 'percentage' => 16.00],
            ['project_id' => $projectIds[1], 'expense_category_id' => 3, 'allocated_amount' => 500000, 'percentage' => 20.00],
            ['project_id' => $projectIds[2], 'expense_category_id' => 4, 'allocated_amount' => 300000, 'percentage' => 20.00],
        ];

        foreach ($budgets as $budget) {
            BudgetAllocation::firstOrCreate($budget);
        }

        $locationIds = Location::pluck('id')->toArray();

        // إنشاء عهد
        if (count($locationIds) >= 2) {
            $custodies = [
                [
                    'custody_number' => 'CUST-2025-001',
                    'project_id' => $projectIds[0],
                    'location_id' => $locationIds[0],
                    'requested_by' => $admin->id,
                    'approved_by' => $admin->id,
                    'amount' => 50000,
                    'remaining_amount' => 50000,
                    'purpose' => 'مصاريف التصوير الخارجي',
                    'request_date' => '2025-01-10',
                    'approval_date' => '2025-01-10',
                    'status' => 'active'
                ],
                [
                    'custody_number' => 'CUST-2025-002',
                    'project_id' => $projectIds[1],
                    'location_id' => $locationIds[1],
                    'requested_by' => $admin->id,
                    'approved_by' => $admin->id,
                    'amount' => 30000,
                    'remaining_amount' => 30000,
                    'purpose' => 'معدات التصوير',
                    'request_date' => '2025-02-05',
                    'approval_date' => '2025-02-05',
                    'status' => 'active'
                ],
            ];

            foreach ($custodies as $custody) {
                Custody::firstOrCreate(['custody_number' => $custody['custody_number']], $custody);
            }
        }

        echo "✅ تم إنشاء المدير العام بنجاح!\n";
        echo "📧 البريد: admin@pems.com\n";
        echo "🔑 كلمة المرور: admin123\n";
        echo "✨ تم إضافة بيانات وهمية شاملة للاختبار\n";
    }
}

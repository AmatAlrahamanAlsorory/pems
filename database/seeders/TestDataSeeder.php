<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Location;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\Person;
use App\Models\Notification;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء المشاريع
        $projects = [
            [
                'name' => 'مسلسل الأحلام الذهبية - رمضان 2025',
                'description' => 'مسلسل درامي اجتماعي من 30 حلقة يتناول قضايا المجتمع المعاصر',
                'total_budget' => 15000000,
                'spent_amount' => 8500000,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(2),
                'status' => 'active'
            ],
            [
                'name' => 'برنامج صباح الخير يا عرب',
                'description' => 'برنامج صباحي يومي يقدم الأخبار والترفيه',
                'total_budget' => 5000000,
                'spent_amount' => 3200000,
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(6),
                'status' => 'active'
            ],
            [
                'name' => 'فيلم الرحلة الأخيرة',
                'description' => 'فيلم سينمائي درامي يحكي قصة عائلة في زمن الحرب',
                'total_budget' => 8000000,
                'spent_amount' => 7800000,
                'start_date' => Carbon::now()->subMonths(8),
                'end_date' => Carbon::now()->subMonth(),
                'status' => 'completed'
            ],
            [
                'name' => 'مسلسل حكايات الأطفال',
                'description' => 'مسلسل تعليمي للأطفال من 20 حلقة',
                'total_budget' => 3000000,
                'spent_amount' => 1200000,
                'start_date' => Carbon::now()->addMonth(),
                'end_date' => Carbon::now()->addMonths(4),
                'status' => 'planned'
            ]
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        // إنشاء المواقع
        $locations = [
            [
                'project_id' => 1,
                'name' => 'استوديو الرياض الرئيسي',
                'address' => 'حي الملز، الرياض، المملكة العربية السعودية',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'budget_allocated' => 2000000
            ],
            [
                'project_id' => 1,
                'name' => 'موقع تصوير خارجي - جدة',
                'address' => 'كورنيش جدة، جدة، المملكة العربية السعودية',
                'latitude' => 21.4858,
                'longitude' => 39.1925,
                'budget_allocated' => 1500000
            ],
            [
                'project_id' => 2,
                'name' => 'استوديو البرامج الصباحية',
                'address' => 'حي العليا، الرياض، المملكة العربية السعودية',
                'latitude' => 24.6877,
                'longitude' => 46.7219,
                'budget_allocated' => 800000
            ],
            [
                'project_id' => 3,
                'name' => 'موقع تصوير الصحراء',
                'address' => 'صحراء الربع الخالي، المملكة العربية السعودية',
                'latitude' => 23.7000,
                'longitude' => 46.7500,
                'budget_allocated' => 1200000
            ]
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }

        // إنشاء الأشخاص
        $people = [
            [
                'name' => 'أحمد محمد العلي',
                'role' => 'مخرج',
                'phone' => '0501234567',
                'email' => 'ahmed.ali@example.com',
                'national_id' => '1234567890'
            ],
            [
                'name' => 'فاطمة سعد الغامدي',
                'role' => 'ممثلة رئيسية',
                'phone' => '0509876543',
                'email' => 'fatima.ghamdi@example.com',
                'national_id' => '0987654321'
            ],
            [
                'name' => 'محمد عبدالله النجار',
                'role' => 'مدير التصوير',
                'phone' => '0555555555',
                'email' => 'mohammed.najjar@example.com',
                'national_id' => '5555555555'
            ],
            [
                'name' => 'نورا خالد الشمري',
                'role' => 'مساعد مخرج',
                'phone' => '0544444444',
                'email' => 'nora.shamri@example.com',
                'national_id' => '4444444444'
            ]
        ];

        foreach ($people as $personData) {
            Person::create($personData);
        }

        // إنشاء العهد
        $custodies = [
            [
                'project_id' => 1,
                'requested_by' => 1,
                'amount' => 500000,
                'purpose' => 'مصروفات تصوير الأسبوع الأول',
                'status' => 'active',
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(5),
                'created_at' => Carbon::now()->subDays(7)
            ],
            [
                'project_id' => 2,
                'requested_by' => 1,
                'amount' => 200000,
                'purpose' => 'مصروفات الإنتاج الشهرية',
                'status' => 'settled',
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(15),
                'settled_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(20)
            ],
            [
                'project_id' => 1,
                'requested_by' => 1,
                'amount' => 300000,
                'purpose' => 'مصروفات المعدات والديكور',
                'status' => 'requested',
                'created_at' => Carbon::now()->subDays(1)
            ]
        ];

        foreach ($custodies as $custodyData) {
            Custody::create($custodyData);
        }

        // إنشاء المصروفات
        $expenses = [
            // مصروفات المشروع الأول
            [
                'project_id' => 1,
                'custody_id' => 1,
                'category_id' => 1, // معدات
                'item_id' => 1,
                'amount' => 150000,
                'description' => 'إيجار كاميرات احترافية لمدة أسبوع',
                'expense_date' => Carbon::now()->subDays(5),
                'location_id' => 1,
                'person_id' => 1,
                'status' => 'approved'
            ],
            [
                'project_id' => 1,
                'custody_id' => 1,
                'category_id' => 2, // مواد استهلاكية
                'item_id' => 5,
                'amount' => 25000,
                'description' => 'مواد تجميل وأزياء للممثلين',
                'expense_date' => Carbon::now()->subDays(4),
                'location_id' => 1,
                'person_id' => 2,
                'status' => 'approved'
            ],
            [
                'project_id' => 1,
                'custody_id' => 1,
                'category_id' => 3, // خدمات
                'item_id' => 9,
                'amount' => 80000,
                'description' => 'خدمات الإضاءة والصوت',
                'expense_date' => Carbon::now()->subDays(3),
                'location_id' => 1,
                'person_id' => 3,
                'status' => 'approved'
            ],
            [
                'project_id' => 1,
                'custody_id' => 1,
                'category_id' => 4, // نقل ومواصلات
                'item_id' => 13,
                'amount' => 45000,
                'description' => 'نقل الفريق والمعدات للموقع',
                'expense_date' => Carbon::now()->subDays(2),
                'location_id' => 2,
                'person_id' => 4,
                'status' => 'approved'
            ],
            // مصروفات المشروع الثاني
            [
                'project_id' => 2,
                'custody_id' => 2,
                'category_id' => 1,
                'item_id' => 2,
                'amount' => 75000,
                'description' => 'صيانة معدات الاستوديو',
                'expense_date' => Carbon::now()->subDays(10),
                'location_id' => 3,
                'person_id' => 1,
                'status' => 'approved'
            ],
            [
                'project_id' => 2,
                'custody_id' => 2,
                'category_id' => 5, // ضيافة
                'item_id' => 17,
                'amount' => 15000,
                'description' => 'ضيافة الضيوف والفريق',
                'expense_date' => Carbon::now()->subDays(8),
                'location_id' => 3,
                'person_id' => 2,
                'status' => 'approved'
            ],
            // مصروفات حديثة
            [
                'project_id' => 1,
                'category_id' => 1,
                'item_id' => 3,
                'amount' => 120000,
                'description' => 'شراء معدات إضاءة جديدة',
                'expense_date' => Carbon::now()->subDay(),
                'location_id' => 1,
                'person_id' => 3,
                'status' => 'pending'
            ],
            [
                'project_id' => 2,
                'category_id' => 2,
                'item_id' => 6,
                'amount' => 35000,
                'description' => 'مواد ديكور للاستوديو',
                'expense_date' => Carbon::now(),
                'location_id' => 3,
                'person_id' => 4,
                'status' => 'pending'
            ]
        ];

        foreach ($expenses as $expenseData) {
            Expense::create($expenseData);
        }

        // إنشاء الإشعارات
        $notifications = [
            [
                'user_id' => 1,
                'title' => 'تجاوز في الميزانية',
                'message' => 'مشروع "فيلم الرحلة الأخيرة" تجاوز 95% من الميزانية المخصصة',
                'type' => 'budget_alert',
                'level' => 'critical',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(2)
            ],
            [
                'user_id' => 1,
                'title' => 'عهدة جديدة تحتاج موافقة',
                'message' => 'طلب عهدة بمبلغ 300,000 ر.س لمشروع "مسلسل الأحلام الذهبية"',
                'type' => 'custody_approval',
                'level' => 'warning',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(5)
            ],
            [
                'user_id' => 1,
                'title' => 'مصروف يحتاج موافقة',
                'message' => 'مصروف بمبلغ 120,000 ر.س لشراء معدات إضاءة',
                'type' => 'expense_approval',
                'level' => 'info',
                'is_read' => false,
                'created_at' => Carbon::now()->subHours(8)
            ],
            [
                'user_id' => 1,
                'title' => 'تم اعتماد المصروف',
                'message' => 'تم اعتماد مصروف خدمات الإضاءة والصوت بمبلغ 80,000 ر.س',
                'type' => 'expense_approved',
                'level' => 'success',
                'is_read' => true,
                'created_at' => Carbon::now()->subDays(1)
            ]
        ];

        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }

        $this->command->info('تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('المشاريع: ' . Project::count());
        $this->command->info('المواقع: ' . Location::count());
        $this->command->info('الأشخاص: ' . Person::count());
        $this->command->info('العهد: ' . Custody::count());
        $this->command->info('المصروفات: ' . Expense::count());
        $this->command->info('الإشعارات: ' . Notification::count());
    }
}
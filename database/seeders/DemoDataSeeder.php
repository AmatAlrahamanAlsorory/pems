<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Location;
use App\Models\Person;
use App\Models\Custody;
use App\Models\Expense;
use App\Models\BudgetAllocation;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // مشروع تجريبي كبير
        $project = Project::create([
            'name' => 'مسلسل الأحلام الذهبية - رمضان 2025',
            'description' => 'مسلسل درامي اجتماعي من 30 حلقة',
            'budget' => 50000000,
            'start_date' => now()->subDays(30),
            'end_date' => now()->addDays(60),
            'status' => 'active'
        ]);

        // مواقع التصوير
        $locations = [
            ['name' => 'استوديو الرياض الرئيسي', 'address' => 'الرياض، حي النرجس', 'budget' => 20000000],
            ['name' => 'موقع جدة التاريخية', 'address' => 'جدة، البلد التاريخية', 'budget' => 15000000],
            ['name' => 'موقع الدمام الساحلي', 'address' => 'الدمام، الكورنيش', 'budget' => 10000000],
        ];

        foreach ($locations as $locationData) {
            Location::create([
                'project_id' => $project->id,
                'name' => $locationData['name'],
                'address' => $locationData['address'],
                'budget' => $locationData['budget']
            ]);
        }

        // أشخاص المشروع
        $people = [
            ['name' => 'أحمد محمد الأحمد', 'role' => 'ممثل رئيسي', 'phone' => '0501234567'],
            ['name' => 'فاطمة علي السالم', 'role' => 'ممثلة رئيسية', 'phone' => '0507654321'],
            ['name' => 'محمد سعد الغامدي', 'role' => 'مدير التصوير', 'phone' => '0509876543'],
            ['name' => 'نورا خالد العتيبي', 'role' => 'مديرة الإنتاج', 'phone' => '0502468135'],
        ];

        foreach ($people as $personData) {
            Person::create([
                'project_id' => $project->id,
                'name' => $personData['name'],
                'role' => $personData['role'],
                'phone' => $personData['phone']
            ]);
        }

        // توزيع الميزانية
        $budgetAllocations = [
            [101, 'بدلات يومية للممثلين', 8000000],
            [201, 'وجبات الفطور للطاقم', 3000000],
            [202, 'وجبات الغداء للطاقم', 4000000],
            [301, 'وقود السيارات', 2000000],
            [401, 'إيجار مواقع تصوير', 15000000],
            [501, 'إيجار كاميرات', 8000000],
            [601, 'شراء أزياء', 5000000],
            [701, 'أجور يومية للفنيين', 3000000],
            [801, 'اتصالات وإنترنت', 1000000],
            [901, 'مصروفات طوارئ طبية', 1000000],
        ];

        foreach ($budgetAllocations as [$categoryId, $description, $amount]) {
            BudgetAllocation::create([
                'project_id' => $project->id,
                'expense_category_id' => $categoryId,
                'allocated_amount' => $amount,
                'description' => $description
            ]);
        }

        // عهد تجريبية
        $custodies = [
            ['amount' => 5000000, 'purpose' => 'مصروفات الأسبوع الأول - استوديو الرياض', 'status' => 'active'],
            ['amount' => 3000000, 'purpose' => 'مصروفات موقع جدة التاريخية', 'status' => 'pending_settlement'],
            ['amount' => 2000000, 'purpose' => 'مصروفات طوارئ ومتنوعة', 'status' => 'settled'],
        ];

        foreach ($custodies as $custodyData) {
            Custody::create([
                'project_id' => $project->id,
                'user_id' => 1, // المدير المالي
                'amount' => $custodyData['amount'],
                'purpose' => $custodyData['purpose'],
                'status' => $custodyData['status'],
                'issued_at' => now()->subDays(rand(1, 20))
            ]);
        }

        // مصروفات تجريبية
        $expenses = [
            [101, 'بدل يومي للممثل الرئيسي', 50000, 'يوم تصوير 15 يناير'],
            [202, 'وجبة غداء للطاقم - 45 شخص', 135000, 'مطعم الذواقة'],
            [301, 'وقود سيارات النقل', 800, 'محطة أرامكو'],
            [401, 'إيجار استوديو يوم إضافي', 25000, 'استوديو الأحلام'],
            [501, 'بطاريات إضافية للكاميرات', 1200, 'متجر التقنية'],
            [601, 'أزياء تراثية للمشهد النهائي', 15000, 'بيت الأزياء التراثية'],
            [701, 'أجر فني الصوت - ساعات إضافية', 2000, '4 ساعات إضافية'],
            [801, 'فاتورة إنترنت الموقع', 500, 'STC Business'],
        ];

        foreach ($expenses as [$categoryId, $description, $amount, $vendor]) {
            Expense::create([
                'project_id' => $project->id,
                'custody_id' => 1,
                'expense_category_id' => $categoryId,
                'amount' => $amount,
                'description' => $description,
                'vendor' => $vendor,
                'expense_date' => now()->subDays(rand(1, 15)),
                'status' => 'approved',
                'created_by' => 1
            ]);
        }
    }
}
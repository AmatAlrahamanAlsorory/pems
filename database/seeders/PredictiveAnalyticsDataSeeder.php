<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\BudgetAllocation;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PredictiveAnalyticsDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');
        
        // إنشاء مشروع تجريبي للتحليلات
        $project = Project::create([
            'name' => 'مشروع تجريبي للتحليلات التنبؤية',
            'description' => 'مشروع لعرض إمكانيات التحليلات التنبؤية في النظام',
            'total_budget' => 2000000, // 2 مليون ريال
            'start_date' => now()->subMonths(3),
            'end_date' => now()->addMonths(2),
            'status' => 'active',
            'currency' => 'SAR'
        ]);

        // إنشاء فئات المصروفات
        $categories = [
            ['code' => 1001, 'name' => 'الممثلين والطاقم', 'color' => '#FF6B6B', 'description' => 'رواتب وأجور الممثلين والطاقم الفني'],
            ['code' => 1002, 'name' => 'المعدات والتقنيات', 'color' => '#4ECDC4', 'description' => 'كاميرات وإضاءة ومعدات صوتية'],
            ['code' => 1003, 'name' => 'المواقع والديكور', 'color' => '#45B7D1', 'description' => 'إيجار المواقع وتكاليف الديكور'],
            ['code' => 1004, 'name' => 'الطعام والضيافة', 'color' => '#96CEB4', 'description' => 'وجبات الطاقم والضيافة'],
            ['code' => 1005, 'name' => 'النقل والمواصلات', 'color' => '#FFEAA7', 'description' => 'تنقل الطاقم والمعدات'],
            ['code' => 1006, 'name' => 'ما بعد الإنتاج', 'color' => '#DDA0DD', 'description' => 'المونتاج والمؤثرات البصرية']
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $cat = ExpenseCategory::firstOrCreate(['name' => $category['name']], $category);
            $categoryIds[] = $cat->id;
        }

        // إنشاء تخصيصات الميزانية
        $budgetAllocations = [
            ['category_id' => $categoryIds[0], 'amount' => 800000], // الممثلين 40%
            ['category_id' => $categoryIds[1], 'amount' => 400000], // المعدات 20%
            ['category_id' => $categoryIds[2], 'amount' => 300000], // المواقع 15%
            ['category_id' => $categoryIds[3], 'amount' => 200000], // الطعام 10%
            ['category_id' => $categoryIds[4], 'amount' => 150000], // النقل 7.5%
            ['category_id' => $categoryIds[5], 'amount' => 150000], // ما بعد الإنتاج 7.5%
        ];

        foreach ($budgetAllocations as $allocation) {
            BudgetAllocation::create([
                'project_id' => $project->id,
                'expense_category_id' => $allocation['category_id'],
                'allocated_amount' => $allocation['amount'],
                'currency' => 'SAR'
            ]);
        }

        // إنشاء مصروفات تاريخية بأنماط واقعية
        $this->createHistoricalExpenses($project, $categoryIds, $faker);
        
        // إنشاء مصروفات حديثة لإظهار الاتجاهات
        $this->createRecentExpenses($project, $categoryIds, $faker);
        
        // إنشاء مصروفات موسمية
        $this->createSeasonalExpenses($project, $categoryIds, $faker);

        $this->command->info('تم إنشاء البيانات التجريبية للتحليلات التنبؤية بنجاح');
    }

    private function createHistoricalExpenses($project, $categoryIds, $faker)
    {
        // إنشاء مصروفات للأشهر الثلاثة الماضية
        for ($month = 3; $month >= 1; $month--) {
            $monthStart = now()->subMonths($month)->startOfMonth();
            $monthEnd = now()->subMonths($month)->endOfMonth();
            
            // عدد المصروفات يزيد تدريجياً (محاكاة تصاعد الإنتاج)
            $expensesCount = 15 + ($month * 5);
            
            for ($i = 0; $i < $expensesCount; $i++) {
                $categoryIndex = $faker->numberBetween(0, count($categoryIds) - 1);
                $categoryId = $categoryIds[$categoryIndex];
                
                // مبالغ مختلفة حسب الفئة
                $amount = $this->getAmountByCategory($categoryIndex, $faker);
                
                // تاريخ عشوائي في الشهر
                $expenseDate = $faker->dateTimeBetween($monthStart, $monthEnd);
                
                Expense::create([
                    'project_id' => $project->id,
                    'expense_category_id' => $categoryId,
                    'description' => $this->getExpenseDescription($categoryIndex, $faker),
                    'amount' => $amount,
                    'expense_date' => $expenseDate,
                    'status' => 'approved',
                    'currency' => 'SAR',
                    'receipt_number' => 'REC-' . $faker->unique()->numberBetween(1000, 9999),
                    'notes' => $faker->optional(0.3)->sentence()
                ]);
            }
        }
    }

    private function createRecentExpenses($project, $categoryIds, $faker)
    {
        // إنشاء مصروفات للشهر الحالي (اتجاه تصاعدي)
        $currentMonth = now()->startOfMonth();
        $today = now();
        
        // مصروفات أكثر في الشهر الحالي
        for ($i = 0; $i < 25; $i++) {
            $categoryIndex = $faker->numberBetween(0, count($categoryIds) - 1);
            $categoryId = $categoryIds[$categoryIndex];
            
            // مبالغ أعلى في الشهر الحالي (محاكاة ذروة الإنتاج)
            $amount = $this->getAmountByCategory($categoryIndex, $faker) * 1.3;
            
            $expenseDate = $faker->dateTimeBetween($currentMonth, $today);
            
            Expense::create([
                'project_id' => $project->id,
                'expense_category_id' => $categoryId,
                'description' => $this->getExpenseDescription($categoryIndex, $faker),
                'amount' => $amount,
                'expense_date' => $expenseDate,
                'status' => 'approved',
                'currency' => 'SAR',
                'receipt_number' => 'REC-' . $faker->unique()->numberBetween(1000, 9999),
                'notes' => $faker->optional(0.3)->sentence()
            ]);
        }
    }

    private function createSeasonalExpenses($project, $categoryIds, $faker)
    {
        // إنشاء مصروفات بأنماط موسمية
        $seasonalPatterns = [
            1 => 1.2, // يناير - بداية السنة
            2 => 1.0, // فبراير
            3 => 1.1, // مارس
            4 => 1.3, // أبريل - رمضان
            5 => 0.9, // مايو
            6 => 1.0, // يونيو
            7 => 1.1, // يوليو
            8 => 0.8, // أغسطس - إجازات
            9 => 1.2, // سبتمبر - عودة النشاط
            10 => 1.1, // أكتوبر
            11 => 1.0, // نوفمبر
            12 => 1.3  // ديسمبر - نهاية السنة
        ];

        foreach ($seasonalPatterns as $month => $factor) {
            if ($month <= now()->month) {
                $monthDate = now()->month($month)->startOfMonth();
                $expensesCount = intval(10 * $factor);
                
                for ($i = 0; $i < $expensesCount; $i++) {
                    $categoryIndex = $faker->numberBetween(0, count($categoryIds) - 1);
                    $categoryId = $categoryIds[$categoryIndex];
                    
                    $amount = $this->getAmountByCategory($categoryIndex, $faker) * $factor;
                    
                    $expenseDate = $faker->dateTimeBetween(
                        $monthDate->copy()->startOfMonth(),
                        $monthDate->copy()->endOfMonth()
                    );
                    
                    Expense::create([
                        'project_id' => $project->id,
                        'expense_category_id' => $categoryId,
                        'description' => $this->getExpenseDescription($categoryIndex, $faker),
                        'amount' => $amount,
                        'expense_date' => $expenseDate,
                        'status' => 'approved',
                        'currency' => 'SAR',
                        'receipt_number' => 'REC-' . $faker->unique()->numberBetween(1000, 9999),
                        'notes' => 'مصروف موسمي - شهر ' . $month
                    ]);
                }
            }
        }
    }

    private function getAmountByCategory($categoryIndex, $faker)
    {
        switch ($categoryIndex) {
            case 0: // الممثلين والطاقم
                return $faker->numberBetween(5000, 50000);
            case 1: // المعدات والتقنيات
                return $faker->numberBetween(2000, 25000);
            case 2: // المواقع والديكور
                return $faker->numberBetween(3000, 20000);
            case 3: // الطعام والضيافة
                return $faker->numberBetween(500, 3000);
            case 4: // النقل والمواصلات
                return $faker->numberBetween(300, 2000);
            case 5: // ما بعد الإنتاج
                return $faker->numberBetween(1000, 15000);
            default:
                return $faker->numberBetween(1000, 10000);
        }
    }

    private function getExpenseDescription($categoryIndex, $faker)
    {
        $descriptions = [
            0 => [ // الممثلين والطاقم
                'راتب الممثل الرئيسي',
                'أجر المخرج اليومي',
                'راتب مساعد المخرج',
                'أجر المصور',
                'راتب فني الصوت',
                'أجر الماكيير',
                'راتب مصمم الأزياء'
            ],
            1 => [ // المعدات والتقنيات
                'إيجار كاميرا احترافية',
                'معدات الإضاءة',
                'أجهزة التسجيل الصوتي',
                'عدسات إضافية',
                'حوامل الكاميرا',
                'معدات الاستديو',
                'أجهزة المونتاج'
            ],
            2 => [ // المواقع والديكور
                'إيجار موقع التصوير',
                'تكاليف الديكور',
                'أثاث المشاهد',
                'إكسسوارات الديكور',
                'تجهيز الموقع',
                'تنظيف الموقع',
                'رسوم التصاريح'
            ],
            3 => [ // الطعام والضيافة
                'وجبات الطاقم',
                'مشروبات وقهوة',
                'وجبة الغداء',
                'وجبات خفيفة',
                'ضيافة الضيوف',
                'مياه وعصائر',
                'حلويات'
            ],
            4 => [ // النقل والمواصلات
                'نقل الطاقم',
                'نقل المعدات',
                'وقود السيارات',
                'أجرة التاكسي',
                'رسوم المواقف',
                'صيانة السيارات',
                'تأمين المركبات'
            ],
            5 => [ // ما بعد الإنتاج
                'خدمات المونتاج',
                'المؤثرات البصرية',
                'تصحيح الألوان',
                'الدبلجة والصوت',
                'الترجمة',
                'النسخ النهائية',
                'التوزيع'
            ]
        ];

        return $faker->randomElement($descriptions[$categoryIndex]);
    }
}
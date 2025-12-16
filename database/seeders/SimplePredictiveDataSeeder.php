<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SimplePredictiveDataSeeder extends Seeder
{
    public function run()
    {
        // التحقق من وجود مشاريع
        $projects = Project::all();
        
        if ($projects->isEmpty()) {
            $this->command->error('لا توجد مشاريع في قاعدة البيانات. يرجى إنشاء مشروع أولاً.');
            return;
        }

        // أخذ أول مشروع متاح
        $project = $projects->first();
        
        // التحقق من وجود فئات المصروفات
        $categories = ExpenseCategory::all();
        
        if ($categories->isEmpty()) {
            $this->command->error('لا توجد فئات مصروفات في قاعدة البيانات.');
            return;
        }

        $this->command->info("إنشاء بيانات تجريبية للمشروع: {$project->name}");

        // إنشاء مصروفات تاريخية للأشهر الثلاثة الماضية
        $this->createHistoricalExpenses($project, $categories);
        
        // إنشاء مصروفات حديثة
        $this->createRecentExpenses($project, $categories);

        $this->command->info('تم إنشاء البيانات التجريبية للتحليلات التنبؤية بنجاح');
    }

    private function createHistoricalExpenses($project, $categories)
    {
        $amounts = [
            0 => [5000, 50000],   // الممثلين والطاقم
            1 => [2000, 25000],   // المعدات والتقنيات
            2 => [3000, 20000],   // المواقع والديكور
            3 => [500, 3000],     // الطعام والضيافة
            4 => [300, 2000],     // النقل والمواصلات
            5 => [1000, 15000],   // ما بعد الإنتاج
        ];

        $descriptions = [
            0 => ['راتب الممثل الرئيسي', 'أجر المخرج اليومي', 'راتب مساعد المخرج'],
            1 => ['إيجار كاميرا احترافية', 'معدات الإضاءة', 'أجهزة التسجيل الصوتي'],
            2 => ['إيجار موقع التصوير', 'تكاليف الديكور', 'أثاث المشاهد'],
            3 => ['وجبات الطاقم', 'مشروبات وقهوة', 'وجبة الغداء'],
            4 => ['نقل الطاقم', 'نقل المعدات', 'وقود السيارات'],
            5 => ['خدمات المونتاج', 'المؤثرات البصرية', 'تصحيح الألوان']
        ];

        // إنشاء مصروفات للأشهر الثلاثة الماضية
        for ($month = 3; $month >= 1; $month--) {
            $monthStart = now()->subMonths($month)->startOfMonth();
            $monthEnd = now()->subMonths($month)->endOfMonth();
            
            // عدد المصروفات يزيد تدريجياً
            $expensesCount = 10 + ($month * 3);
            
            for ($i = 0; $i < $expensesCount; $i++) {
                $categoryIndex = rand(0, min(5, $categories->count() - 1));
                $category = $categories->skip($categoryIndex)->first();
                
                if (!$category) continue;
                
                // مبلغ عشوائي حسب الفئة
                $minAmount = $amounts[$categoryIndex][0] ?? 1000;
                $maxAmount = $amounts[$categoryIndex][1] ?? 10000;
                $amount = rand($minAmount, $maxAmount);
                
                // وصف عشوائي
                $description = $descriptions[$categoryIndex][rand(0, 2)] ?? 'مصروف متنوع';
                
                // تاريخ عشوائي في الشهر
                $expenseDate = $monthStart->copy()->addDays(rand(0, $monthStart->daysInMonth - 1));
                
                try {
                    Expense::create([
                        'project_id' => $project->id,
                        'expense_category_id' => $category->id,
                        'description' => $description,
                        'amount' => $amount,
                        'expense_date' => $expenseDate,
                        'status' => 'approved',
                        'currency' => 'SAR',
                        'receipt_number' => 'REC-' . rand(1000, 9999) . '-' . $i,
                        'notes' => 'بيانات تجريبية - شهر ' . $month
                    ]);
                } catch (\Exception $e) {
                    // تجاهل الأخطاء والمتابعة
                    continue;
                }
            }
        }
    }

    private function createRecentExpenses($project, $categories)
    {
        // إنشاء مصروفات للشهر الحالي
        $currentMonth = now()->startOfMonth();
        $today = now();
        
        for ($i = 0; $i < 15; $i++) {
            $category = $categories->random();
            
            $amount = rand(2000, 30000);
            $expenseDate = $currentMonth->copy()->addDays(rand(0, $today->day - 1));
            
            try {
                Expense::create([
                    'project_id' => $project->id,
                    'expense_category_id' => $category->id,
                    'description' => 'مصروف حديث - ' . $category->name,
                    'amount' => $amount,
                    'expense_date' => $expenseDate,
                    'status' => 'approved',
                    'currency' => 'SAR',
                    'receipt_number' => 'REC-CUR-' . rand(1000, 9999) . '-' . $i,
                    'notes' => 'بيانات تجريبية - الشهر الحالي'
                ]);
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
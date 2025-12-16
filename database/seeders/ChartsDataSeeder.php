<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use App\Models\Expense;
use App\Models\Person;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ChartsDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎯 إضافة بيانات المخططات البيانية فقط...');
        
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $items = ExpenseItem::all();
        $people = Person::all();
        $locations = Location::all();
        
        if ($projects->isEmpty() || $categories->isEmpty()) {
            $this->command->error('❌ لا توجد مشاريع أو فئات مصروفات. شغل SimpleTestDataSeeder أولاً');
            return;
        }
        
        $this->addChartsExpenses($projects, $categories, $items, $people, $locations);
        
        $this->command->info('✅ تم إضافة بيانات المخططات بنجاح!');
    }
    
    private function addChartsExpenses($projects, $categories, $items, $people, $locations)
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
        
        $expenseNumber = Expense::count() + 1;
        
        // إنشاء مصروفات لكل شهر
        foreach ($monthlyAmounts as $month => $amounts) {
            foreach ($amounts as $amount) {
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
                    'status' => 'approved',
                    'created_by' => 1
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
                        'status' => 'approved',
                        'created_by' => 1
                    ]);
                }
            }
        }
    }
}
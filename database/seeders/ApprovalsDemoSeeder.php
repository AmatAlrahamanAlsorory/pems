<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Approval;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\Project;
use App\Models\ExpenseCategory;
use App\Models\User;

class ApprovalsDemoSeeder extends Seeder
{
    public function run()
    {
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $users = User::all();
        
        if ($projects->isEmpty() || $categories->isEmpty() || $users->isEmpty()) {
            $this->command->error('تحتاج إلى مشاريع وفئات ومستخدمين في قاعدة البيانات');
            return;
        }

        $project = $projects->first();
        $user = $users->first();

        // إنشاء مصروفات تحتاج موافقة
        $expensesData = [
            [
                'description' => 'معدات إضاءة احترافية للمشاهد الليلية',
                'amount' => 18500,
                'category_id' => $categories->where('name', 'LIKE', '%معدات%')->first()->id ?? $categories->first()->id
            ],
            [
                'description' => 'أجور الممثلين الإضافيين لمشهد الحشود',
                'amount' => 12000,
                'category_id' => $categories->where('name', 'LIKE', '%ممثل%')->first()->id ?? $categories->first()->id
            ],
            [
                'description' => 'إيجار موقع تصوير تاريخي لمدة 3 أيام',
                'amount' => 25000,
                'category_id' => $categories->where('name', 'LIKE', '%موقع%')->first()->id ?? $categories->first()->id
            ]
        ];

        foreach ($expensesData as $expenseData) {
            $expense = Expense::create([
                'project_id' => $project->id,
                'expense_category_id' => $expenseData['category_id'],
                'description' => $expenseData['description'],
                'amount' => $expenseData['amount'],
                'expense_date' => now()->subDays(rand(1, 5)),
                'status' => 'pending',
                'currency' => 'SAR',
                'expense_number' => 'EXP-PEND-' . rand(1000, 9999)
            ]);

            // إنشاء طلب موافقة
            Approval::create([
                'approvable_type' => 'App\\Models\\Expense',
                'approvable_id' => $expense->id,
                'status' => 'pending',
                'requested_by' => $user->id,
                'requested_at' => $expense->created_at
            ]);
        }

        // إنشاء عهد تحتاج موافقة
        $custodiesData = [
            [
                'description' => 'عهدة نقدية لشراء مستلزمات الإنتاج الطارئة',
                'amount' => 15000
            ],
            [
                'description' => 'عهدة لتغطية مصاريف النقل والمواصلات',
                'amount' => 8500
            ]
        ];

        foreach ($custodiesData as $custodyData) {
            $custody = Custody::create([
                'project_id' => $project->id,
                'requested_by' => $user->id,
                'amount' => $custodyData['amount'],
                'description' => $custodyData['description'],
                'status' => 'requested',
                'currency' => 'SAR',
                'request_date' => now()->subDays(rand(1, 3))
            ]);

            // إنشاء طلب موافقة
            Approval::create([
                'approvable_type' => 'App\\Models\\Custody',
                'approvable_id' => $custody->id,
                'status' => 'pending',
                'requested_by' => $user->id,
                'requested_at' => $custody->request_date
            ]);
        }

        $this->command->info('تم إنشاء الموافقات التجريبية بنجاح');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Expense;
use App\Models\Custody;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // مستخدم تجريبي
        User::create([
            'name' => 'مدير مالي تجريبي',
            'email' => 'admin@pems.com',
            'password' => bcrypt('demo123'),
            'role' => 'financial_manager'
        ]);

        // مشروع تجريبي
        $project = Project::create([
            'name' => 'مسلسل الأحلام - عرض تجريبي',
            'type' => 'series',
            'total_budget' => 1000000,
            'spent_amount' => 650000,
            'emergency_reserve' => 100000,
            'episodes_count' => 30,
            'status' => 'active',
            'start_date' => now()->subDays(30),
            'end_date' => now()->addDays(60)
        ]);

        // مصروفات تجريبية
        for ($i = 1; $i <= 10; $i++) {
            Expense::create([
                'expense_number' => 'DEMO-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'project_id' => $project->id,
                'expense_category_id' => rand(1, 9),
                'amount' => rand(5000, 50000),
                'currency' => 'YER',
                'description' => 'مصروف تجريبي رقم ' . $i,
                'expense_date' => now()->subDays(rand(1, 30)),
                'status' => 'approved',
                'created_by' => 1
            ]);
        }
    }
}
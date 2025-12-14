<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Expense;
use App\Models\Approval;
use Illuminate\Database\Seeder;

class CriticalDataSeeder extends Seeder
{
    public function run(): void
    {
        // تحديث مشروع ليكون حرج
        $project = Project::first();
        if ($project) {
            $project->spent_amount = $project->total_budget * 0.92;
            $project->save();
            echo "✅ تم تحديث مشروع: {$project->name}\n";
        }

        // إنشاء موافقات معلقة
        $expenses = Expense::where('status', 'pending')->limit(3)->get();
        foreach ($expenses as $expense) {
            Approval::create([
                'approvable_type' => 'App\Models\Expense',
                'approvable_id' => $expense->id,
                'user_id' => 1,
                'status' => 'pending',
            ]);
        }

        echo "✅ تم إضافة " . $expenses->count() . " موافقات معلقة\n";
    }
}

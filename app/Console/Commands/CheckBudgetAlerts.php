<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Notification;

class CheckBudgetAlerts extends Command
{
    protected $signature = 'budget:check-alerts';
    protected $description = 'فحص تجاوز الميزانيات وإرسال التنبيهات';

    public function handle()
    {
        $this->info('بدء فحص الميزانيات...');
        
        $projects = Project::where('status', 'active')->get();
        $alertsCount = 0;
        
        foreach ($projects as $project) {
            $percentage = $project->budget_percentage;
            
            if ($percentage >= 100) {
                $this->sendCriticalBudgetAlert($project);
                $alertsCount++;
            } elseif ($percentage >= 90) {
                $this->sendDangerBudgetAlert($project);
                $alertsCount++;
            } elseif ($percentage >= 70) {
                $this->sendWarningBudgetAlert($project);
                $alertsCount++;
            }
        }
        
        $this->info("تم فحص {$projects->count()} مشروع وإرسال {$alertsCount} تنبيه");
        
        return 0;
    }
    
    private function sendWarningBudgetAlert(Project $project)
    {
        $this->sendBudgetAlert($project, 'warning', 'تحذير: اقتراب من حد الميزانية', 'وصل المشروع إلى 70% من الميزانية');
    }
    
    private function sendDangerBudgetAlert(Project $project)
    {
        $this->sendBudgetAlert($project, 'danger', 'خطر: تجاوز 90% من الميزانية', 'وصل المشروع إلى 90% من الميزانية - مراجعة فورية مطلوبة');
    }
    
    private function sendCriticalBudgetAlert(Project $project)
    {
        $this->sendBudgetAlert($project, 'critical', 'حرج: تجاوز الميزانية', 'تم تجاوز ميزانية المشروع! إيقاف الصرف مطلوب');
        
        // تحديث حالة المشروع
        $project->update(['status' => 'blocked']);
    }
    
    private function sendBudgetAlert(Project $project, $level, $title, $message)
    {
        // تجنب الإشعارات المكررة
        $exists = Notification::where('type', 'budget_alert')
            ->where('data->project_id', $project->id)
            ->where('level', $level)
            ->where('created_at', '>=', now()->subHours(6))
            ->exists();
            
        if ($exists) return;
        
        // إشعار لجميع المستخدمين المعنيين
        $users = \App\Models\User::whereIn('role', ['financial_manager', 'admin_accountant', 'production_manager'])->get();
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => "{$message} - مشروع: {$project->name} ({$project->budget_percentage}%)",
                'type' => 'budget_alert',
                'level' => $level,
                'data' => json_encode([
                    'project_id' => $project->id,
                    'percentage' => $project->budget_percentage,
                    'spent' => $project->spent_amount,
                    'total' => $project->total_budget
                ])
            ]);
        }
    }
}
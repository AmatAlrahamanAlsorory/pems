<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Custody;
use App\Models\Notification;
use App\Models\User;

class AlertService
{
    public function checkAllAlerts()
    {
        $this->checkBudgetAlerts();
        $this->checkOverdueCustodies();
        $this->checkUnsettledCustodies();
        $this->checkWeeklyCustodySettlement();
    }
    
    public function checkWeeklyCustodySettlement()
    {
        $custodyRules = app(CustodyRulesService::class);
        $custodyRules->checkWeeklySettlement();
    }
    
    public function checkBudgetAlerts()
    {
        $projects = Project::where('status', '!=', 'completed')->get();
        
        foreach ($projects as $project) {
            $percentage = $project->budget_percentage;
            $this->handleBudgetAlert($project, $percentage);
        }
    }
    
    public function checkOverdueCustodies()
    {
        $overdueCustodies = Custody::where('status', 'active')
            ->where('created_at', '<', now()->subDays(7))
            ->with(['user', 'project'])
            ->get();
        
        foreach ($overdueCustodies as $custody) {
            $this->createOverdueAlert($custody);
            $custody->update(['status' => 'overdue']);
        }
    }
    
    public function checkUnsettledCustodies()
    {
        $unsettled = Custody::where('status', 'active')
            ->where('created_at', '<', now()->subDays(14))
            ->with(['user', 'project'])
            ->get();
        
        foreach ($unsettled as $custody) {
            $this->createCriticalCustodyAlert($custody);
        }
    }
    
    private function handleBudgetAlert(Project $project, float $percentage)
    {
        if ($percentage >= 100) {
            $this->createCriticalAlert($project, $percentage);
            $this->blockProjectSpending($project);
        } elseif ($percentage >= 90) {
            $this->createDangerAlert($project, $percentage);
        } elseif ($percentage >= 70) {
            $this->createWarningAlert($project, $percentage);
        }
    }
    
    private function createCriticalAlert(Project $project, float $percentage)
    {
        $users = User::whereIn('role', ['financial_manager', 'admin_accountant'])->get();
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'تجاوز حرج في الميزانية',
                'message' => "المشروع '{$project->name}' تجاوز الميزانية بنسبة " . number_format($percentage, 1) . "%",
                'type' => 'critical',
                'data' => json_encode(['project_id' => $project->id, 'percentage' => $percentage])
            ]);
        }
    }
    
    private function createDangerAlert(Project $project, float $percentage)
    {
        $users = User::whereIn('role', ['financial_manager', 'admin_accountant', 'production_manager'])->get();
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'تحذير خطر في الميزانية',
                'message' => "المشروع '{$project->name}' وصل إلى " . number_format($percentage, 1) . "% من الميزانية",
                'type' => 'danger',
                'data' => json_encode(['project_id' => $project->id, 'percentage' => $percentage])
            ]);
        }
    }
    
    private function createWarningAlert(Project $project, float $percentage)
    {
        $users = User::where('role', 'production_manager')->get();
        
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'تحذير في الميزانية',
                'message' => "المشروع '{$project->name}' وصل إلى " . number_format($percentage, 1) . "% من الميزانية",
                'type' => 'warning',
                'data' => json_encode(['project_id' => $project->id, 'percentage' => $percentage])
            ]);
        }
    }
    
    private function blockProjectSpending(Project $project)
    {
        $project->update(['is_blocked' => true]);
    }
    
    public function canSpendOnProject(Project $project): bool
    {
        return !$project->is_blocked && $project->budget_percentage < 100;
    }
    
    private function createOverdueAlert(Custody $custody)
    {
        $daysOverdue = now()->diffInDays($custody->created_at);
        
        Notification::create([
            'user_id' => $custody->user_id,
            'title' => 'عهدة متأخرة في التصفية',
            'message' => "العهدة رقم {$custody->id} متأخرة {$daysOverdue} يوم. يرجى التصفية فوراً.",
            'type' => 'warning',
            'data' => json_encode(['custody_id' => $custody->id, 'days_overdue' => $daysOverdue])
        ]);
        
        // إشعار للإدارة
        $managers = User::role(['financial_manager', 'admin_accountant'])->get();
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => 'عهدة متأخرة',
                'message' => "العهدة رقم {$custody->id} للمستخدم {$custody->user->name} متأخرة {$daysOverdue} يوم",
                'type' => 'warning',
                'data' => json_encode(['custody_id' => $custody->id])
            ]);
        }
    }
    
    private function createCriticalCustodyAlert(Custody $custody)
    {
        $daysOverdue = now()->diffInDays($custody->created_at);
        
        $managers = User::role(['financial_manager'])->get();
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => 'عهدة حرجة - تجاوزت أسبوعين',
                'message' => "العهدة رقم {$custody->id} متأخرة {$daysOverdue} يوم. تتطلب إجراء عاجل.",
                'type' => 'critical',
                'data' => json_encode(['custody_id' => $custody->id, 'days_overdue' => $daysOverdue])
            ]);
        }
    }
}
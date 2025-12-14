<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Project;
use App\Models\Location;
use App\Models\Custody;
use App\Models\User;

class NotificationService
{
    public function checkBudgetAlerts()
    {
        // فحص تجاوز الميزانية للمشاريع
        $projects = Project::all();
        foreach ($projects as $project) {
            $percentage = $project->budget_percentage;
            
            if ($percentage >= 100) {
                $this->createNotification([
                    'users' => User::whereIn('role', ['financial_manager', 'admin_accountant'])->get(),
                    'type' => 'budget_exceeded',
                    'title' => 'تجاوز الميزانية',
                    'message' => "المشروع {$project->name} تجاوز الميزانية المحددة",
                    'level' => 'critical',
                    'data' => ['project_id' => $project->id, 'percentage' => $percentage]
                ]);
            } elseif ($percentage >= 90) {
                $this->createNotification([
                    'users' => User::whereIn('role', ['financial_manager', 'admin_accountant', 'production_manager'])->get(),
                    'type' => 'budget_warning',
                    'title' => 'تحذير الميزانية',
                    'message' => "المشروع {$project->name} اقترب من حد الميزانية ({$percentage}%)",
                    'level' => 'danger',
                    'data' => ['project_id' => $project->id, 'percentage' => $percentage]
                ]);
            } elseif ($percentage >= 70) {
                $this->createNotification([
                    'users' => User::where('role', 'production_manager')->get(),
                    'type' => 'budget_warning',
                    'title' => 'تنبيه الميزانية',
                    'message' => "المشروع {$project->name} وصل إلى {$percentage}% من الميزانية",
                    'level' => 'warning',
                    'data' => ['project_id' => $project->id, 'percentage' => $percentage]
                ]);
            }
            
            // فحص تجاوز فئات الميزانية
            foreach ($project->budgetAllocations as $allocation) {
                $usage = $allocation->usage_percentage;
                if ($usage >= 100) {
                    $this->createNotification([
                        'users' => User::whereIn('role', ['financial_manager', 'admin_accountant'])->get(),
                        'type' => 'category_budget_exceeded',
                        'title' => 'تجاوز ميزانية الفئة',
                        'message' => "فئة {$allocation->category->name} في مشروع {$project->name} تجاوزت الميزانية",
                        'level' => 'critical',
                        'data' => ['project_id' => $project->id, 'category_id' => $allocation->expense_category_id]
                    ]);
                } elseif ($usage >= 90) {
                    $this->createNotification([
                        'users' => User::where('role', 'production_manager')->get(),
                        'type' => 'category_budget_warning',
                        'title' => 'تحذير ميزانية الفئة',
                        'message' => "فئة {$allocation->category->name} وصلت إلى {$usage}% من الميزانية",
                        'level' => 'warning',
                        'data' => ['project_id' => $project->id, 'category_id' => $allocation->expense_category_id]
                    ]);
                }
            }
        }

        // فحص العهد المتأخرة
        $overdueCustodies = Custody::where('status', 'approved')
            ->where('created_at', '<', now()->subWeeks(2))
            ->get();

        foreach ($overdueCustodies as $custody) {
            $this->createNotification([
                'users' => [$custody->user, ...User::whereIn('role', ['financial_manager', 'admin_accountant'])->get()],
                'type' => 'custody_overdue',
                'title' => 'عهدة متأخرة',
                'message' => "العهدة رقم {$custody->id} متأخرة في التصفية",
                'level' => 'danger',
                'data' => ['custody_id' => $custody->id]
            ]);
        }
    }

    private function createNotification($data)
    {
        foreach ($data['users'] as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'title' => $data['title'],
                'message' => $data['message'],
                'level' => $data['level'],
                'data' => $data['data'] ?? null,
            ]);
        }
    }
}
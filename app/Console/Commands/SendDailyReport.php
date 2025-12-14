<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\Notification;

class SendDailyReport extends Command
{
    protected $signature = 'report:daily';
    protected $description = 'إرسال تقرير يومي للإدارة';

    public function handle()
    {
        $todayExpenses = Expense::whereDate('created_at', today())->sum('amount');
        $activeCustodies = Custody::where('status', 'active')->count();
        $pendingApprovals = \App\Models\Approval::where('status', 'pending')->count();
        
        $managers = User::role(['financial_manager', 'admin_accountant'])->get();
        
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => 'التقرير اليومي',
                'message' => "مصروفات اليوم: " . number_format($todayExpenses, 2) . " ريال | عهد نشطة: {$activeCustodies} | موافقات معلقة: {$pendingApprovals}",
                'type' => 'info',
                'data' => json_encode([
                    'today_expenses' => $todayExpenses,
                    'active_custodies' => $activeCustodies,
                    'pending_approvals' => $pendingApprovals
                ])
            ]);
        }
        
        $this->info('تم إرسال التقرير اليومي!');
        return 0;
    }
}

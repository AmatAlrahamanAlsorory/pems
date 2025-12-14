<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Custody;
use App\Models\Notification;
use App\Services\CustodyRulesService;

class CheckOverdueCustodies extends Command
{
    protected $signature = 'custodies:check-overdue';
    protected $description = 'فحص العهد المتأخرة وإرسال التنبيهات';

    public function handle()
    {
        $this->info('بدء فحص العهد المتأخرة...');
        
        $custodyRules = app(CustodyRulesService::class);
        
        // العهد النشطة
        $activeCustodies = Custody::where('status', 'active')
            ->with(['requestedBy', 'project'])
            ->get();
        
        $warningCount = 0;
        $criticalCount = 0;
        
        foreach ($activeCustodies as $custody) {
            $daysOpen = now()->diffInDays($custody->created_at);
            
            // تحذير بعد 7 أيام
            if ($daysOpen >= CustodyRulesService::OVERDUE_WARNING_DAYS && $daysOpen < CustodyRulesService::OVERDUE_CRITICAL_DAYS) {
                $this->sendWarningNotification($custody, $daysOpen);
                $warningCount++;
            }
            
            // حرج بعد 14 يوم
            if ($daysOpen >= CustodyRulesService::OVERDUE_CRITICAL_DAYS) {
                $this->sendCriticalNotification($custody, $daysOpen);
                $custody->update(['status' => 'overdue']);
                $criticalCount++;
            }
        }
        
        // فحص التصفية الأسبوعية
        $custodyRules->checkWeeklySettlement();
        
        $this->info("تم إرسال {$warningCount} تحذير و {$criticalCount} تنبيه حرج");
        
        return 0;
    }
    
    private function sendWarningNotification(Custody $custody, $days)
    {
        // تحقق من عدم إرسال نفس التنبيه اليوم
        $exists = Notification::where('user_id', $custody->requested_by)
            ->where('type', 'warning')
            ->whereDate('created_at', today())
            ->where('data->custody_id', $custody->id)
            ->exists();
            
        if ($exists) return;
        
        Notification::create([
            'user_id' => $custody->requested_by,
            'title' => '⚠️ تحذير: عهدة متأخرة',
            'message' => "العهدة رقم {$custody->custody_number} مفتوحة منذ {$days} يوم. يرجى التصفية في أقرب وقت.",
            'type' => 'warning',
            'data' => json_encode(['custody_id' => $custody->id, 'days' => $days])
        ]);
        
        // إشعار للمدير المالي
        $financialManagers = \App\Models\User::role('financial_manager')->get();
        foreach ($financialManagers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => '⚠️ عهدة متأخرة',
                'message' => "العهدة {$custody->custody_number} للمستخدم {$custody->requestedBy->name} متأخرة {$days} يوم",
                'type' => 'warning',
                'data' => json_encode(['custody_id' => $custody->id])
            ]);
        }
    }
    
    private function sendCriticalNotification(Custody $custody, $days)
    {
        Notification::create([
            'user_id' => $custody->requested_by,
            'title' => '🔴 حرج: عهدة متأخرة جداً',
            'message' => "العهدة رقم {$custody->custody_number} متأخرة {$days} يوم! التصفية إجبارية فوراً.",
            'type' => 'critical',
            'data' => json_encode(['custody_id' => $custody->id, 'days' => $days])
        ]);
        
        // إشعار عاجل للإدارة
        $managers = \App\Models\User::role(['financial_manager', 'admin_accountant'])->get();
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => '🔴 عهدة حرجة',
                'message' => "العهدة {$custody->custody_number} متأخرة {$days} يوم - تدخل فوري مطلوب",
                'type' => 'critical',
                'data' => json_encode(['custody_id' => $custody->id])
            ]);
        }
    }
}

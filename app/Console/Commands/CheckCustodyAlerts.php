<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Custody;
use App\Models\Notification;
use App\Services\CustodyRulesService;

class CheckCustodyAlerts extends Command
{
    protected $signature = 'custody:check-alerts';
    protected $description = 'فحص العهد المتأخرة وإرسال التنبيهات';

    public function handle()
    {
        $this->info('بدء فحص العهد المتأخرة...');
        
        $custodyRules = app(CustodyRulesService::class);
        
        // العهد المتأخرة 7 أيام (تحذير)
        $warningCustodies = Custody::where('status', 'active')
            ->where('created_at', '<', now()->subDays(7))
            ->where('created_at', '>=', now()->subDays(14))
            ->with(['requestedBy', 'project'])
            ->get();
            
        foreach ($warningCustodies as $custody) {
            $this->sendOverdueWarning($custody);
        }
        
        // العهد المتأخرة 14 يوم (حرج)
        $criticalCustodies = Custody::where('status', 'active')
            ->where('created_at', '<', now()->subDays(14))
            ->with(['requestedBy', 'project'])
            ->get();
            
        foreach ($criticalCustodies as $custody) {
            $this->sendCriticalAlert($custody);
        }
        
        // فحص التصفية الأسبوعية
        $custodyRules->checkWeeklySettlement();
        
        $this->info("تم فحص {$warningCustodies->count()} عهدة تحذيرية و {$criticalCustodies->count()} عهدة حرجة");
        
        return 0;
    }
    
    private function sendOverdueWarning(Custody $custody)
    {
        // تجنب الإشعارات المكررة
        $exists = Notification::where('user_id', $custody->requested_by)
            ->where('type', 'warning')
            ->where('data->custody_id', $custody->id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();
            
        if ($exists) return;
        
        Notification::create([
            'user_id' => $custody->requested_by,
            'title' => 'تحذير: عهدة متأخرة في التصفية',
            'message' => "العهدة رقم {$custody->custody_number} متأخرة 7 أيام. يرجى التصفية فوراً.",
            'type' => 'warning',
            'level' => 'warning',
            'data' => json_encode(['custody_id' => $custody->id])
        ]);
    }
    
    private function sendCriticalAlert(Custody $custody)
    {
        $exists = Notification::where('user_id', $custody->requested_by)
            ->where('type', 'critical')
            ->where('data->custody_id', $custody->id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();
            
        if ($exists) return;
        
        Notification::create([
            'user_id' => $custody->requested_by,
            'title' => 'حرج: عهدة متأخرة جداً',
            'message' => "العهدة رقم {$custody->custody_number} متأخرة أكثر من 14 يوم! تصفية إجبارية مطلوبة.",
            'type' => 'critical',
            'level' => 'critical',
            'data' => json_encode(['custody_id' => $custody->id])
        ]);
        
        // إشعار للمدير المالي أيضاً
        $financialManager = \App\Models\User::where('role', 'financial_manager')->first();
        if ($financialManager) {
            Notification::create([
                'user_id' => $financialManager->id,
                'title' => 'تنبيه إداري: عهدة متأخرة جداً',
                'message' => "العهدة رقم {$custody->custody_number} للمستخدم {$custody->requestedBy->name} متأخرة أكثر من 14 يوم",
                'type' => 'admin_alert',
                'level' => 'critical',
                'data' => json_encode(['custody_id' => $custody->id, 'user_id' => $custody->requested_by])
            ]);
        }
    }
}
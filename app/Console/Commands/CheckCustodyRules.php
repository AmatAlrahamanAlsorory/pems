<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CustodyRulesService;
use App\Models\Custody;
use App\Models\Notification;

class CheckCustodyRules extends Command
{
    protected $signature = 'custody:check-rules';
    protected $description = 'فحص قواعد العهد وإرسال التنبيهات';

    public function handle()
    {
        $custodyRules = app(CustodyRulesService::class);
        
        // 1. فحص التصفية الأسبوعية
        $this->info('فحص التصفية الأسبوعية...');
        $custodyRules->checkWeeklySettlement();
        
        // 2. فحص العهد المتأخرة
        $this->info('فحص العهد المتأخرة...');
        $this->checkOverdueCustodies();
        
        // 3. فحص العهد الحرجة
        $this->info('فحص العهد الحرجة...');
        $this->checkCriticalCustodies();
        
        $this->info('تم الفحص بنجاح!');
    }
    
    private function checkOverdueCustodies()
    {
        $overdue = Custody::where('status', 'active')
            ->where('created_at', '<', now()->subDays(CustodyRulesService::OVERDUE_WARNING_DAYS))
            ->with('requestedBy')
            ->get();
        
        foreach ($overdue as $custody) {
            $days = now()->diffInDays($custody->created_at);
            
            Notification::create([
                'user_id' => $custody->requested_by,
                'title' => 'تحذير: عهدة متأخرة',
                'message' => "العهدة رقم {$custody->custody_number} متأخرة {$days} يوم. يرجى التصفية فوراً.",
                'type' => 'warning',
                'data' => json_encode(['custody_id' => $custody->id, 'days' => $days])
            ]);
        }
        
        $this->info("تم إرسال {$overdue->count()} تنبيه للعهد المتأخرة");
    }
    
    private function checkCriticalCustodies()
    {
        $critical = Custody::where('status', 'active')
            ->where('created_at', '<', now()->subDays(CustodyRulesService::OVERDUE_CRITICAL_DAYS))
            ->with(['requestedBy', 'project'])
            ->get();
        
        foreach ($critical as $custody) {
            // تنبيه للمستخدم
            Notification::create([
                'user_id' => $custody->requested_by,
                'title' => '⚠️ عهدة حرجة - تصفية إجبارية',
                'message' => "العهدة رقم {$custody->custody_number} متأخرة أكثر من أسبوعين. التصفية إجبارية.",
                'type' => 'critical',
                'data' => json_encode(['custody_id' => $custody->id])
            ]);
            
            // تنبيه للإدارة
            $managers = \App\Models\User::role(['financial_manager', 'admin_accountant'])->get();
            foreach ($managers as $manager) {
                Notification::create([
                    'user_id' => $manager->id,
                    'title' => 'عهدة حرجة تحتاج متابعة',
                    'message' => "العهدة {$custody->custody_number} للمستخدم {$custody->requestedBy->name} متأخرة جداً.",
                    'type' => 'critical',
                    'data' => json_encode(['custody_id' => $custody->id])
                ]);
            }
        }
        
        $this->info("تم إرسال تنبيهات لـ {$critical->count()} عهدة حرجة");
    }
}

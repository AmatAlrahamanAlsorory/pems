<?php

namespace App\Services;

use App\Models\Custody;
use App\Models\User;
use App\Models\Notification;

class CustodyRulesService
{
    // القواعد
    const MAX_OPEN_CUSTODIES = 2;
    const MIN_SETTLEMENT_PERCENTAGE = 80;
    const OVERDUE_WARNING_DAYS = 7;
    const OVERDUE_CRITICAL_DAYS = 14;
    const WEEKLY_SETTLEMENT_REQUIRED = true;
    
    /**
     * التحقق من إمكانية طلب عهدة جديدة
     */
    public function canRequestNewCustody(User $user, $projectId = null)
    {
        $errors = [];
        
        // 1. فحص عدد العهد المفتوحة
        $openCustodies = $this->getOpenCustodies($user, $projectId);
        if ($openCustodies->count() >= self::MAX_OPEN_CUSTODIES) {
            $errors[] = "لديك {$openCustodies->count()} عهدة مفتوحة. الحد الأقصى " . self::MAX_OPEN_CUSTODIES;
        }
        
        // 2. فحص نسبة التصفية
        foreach ($openCustodies as $custody) {
            $settlementPercentage = $this->getSettlementPercentage($custody);
            if ($settlementPercentage < self::MIN_SETTLEMENT_PERCENTAGE) {
                $errors[] = "العهدة رقم {$custody->custody_number} مصفاة بنسبة {$settlementPercentage}% فقط. يجب تصفية " . self::MIN_SETTLEMENT_PERCENTAGE . "% قبل طلب عهدة جديدة";
            }
        }
        
        // 3. فحص العهد المتأخرة
        $overdueCustodies = $this->getOverdueCustodies($user);
        if ($overdueCustodies->count() > 0) {
            $errors[] = "لديك {$overdueCustodies->count()} عهدة متأخرة في التصفية. يجب تصفيتها أولاً";
        }
        
        return [
            'allowed' => empty($errors),
            'errors' => $errors,
            'open_custodies' => $openCustodies->count(),
            'overdue_custodies' => $overdueCustodies->count()
        ];
    }
    
    /**
     * الحصول على العهد المفتوحة
     */
    public function getOpenCustodies(User $user, $projectId = null)
    {
        $query = Custody::where('requested_by', $user->id)
            ->whereIn('status', ['requested', 'approved', 'active']);
            
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        return $query->get();
    }
    
    /**
     * حساب نسبة التصفية
     */
    public function getSettlementPercentage(Custody $custody)
    {
        if ($custody->amount == 0) return 0;
        
        return ($custody->spent_amount / $custody->amount) * 100;
    }
    
    /**
     * الحصول على العهد المتأخرة
     */
    public function getOverdueCustodies(User $user)
    {
        return Custody::where('requested_by', $user->id)
            ->where('status', 'active')
            ->where('created_at', '<', now()->subDays(self::OVERDUE_WARNING_DAYS))
            ->get();
    }
    
    /**
     * فحص العهد التي تحتاج تصفية أسبوعية
     */
    public function checkWeeklySettlement()
    {
        if (!self::WEEKLY_SETTLEMENT_REQUIRED) return;
        
        $custodies = Custody::where('status', 'active')
            ->where('created_at', '<', now()->subDays(7))
            ->whereDoesntHave('expenses', function($q) {
                $q->where('created_at', '>=', now()->subDays(7));
            })
            ->with(['requestedBy', 'project'])
            ->get();
        
        foreach ($custodies as $custody) {
            $this->sendWeeklySettlementReminder($custody);
        }
    }
    
    /**
     * إرسال تذكير التصفية الأسبوعية
     */
    private function sendWeeklySettlementReminder(Custody $custody)
    {
        Notification::create([
            'user_id' => $custody->requested_by,
            'title' => 'تذكير: التصفية الأسبوعية مطلوبة',
            'message' => "العهدة رقم {$custody->custody_number} تحتاج تصفية أسبوعية. يرجى تقديم التصفية.",
            'type' => 'warning',
            'data' => json_encode(['custody_id' => $custody->id])
        ]);
    }
    
    /**
     * تطبيق قواعد الموافقة على العهدة
     */
    public function applyApprovalRules(Custody $custody)
    {
        $rules = [];
        
        // قاعدة المبلغ
        if ($custody->amount > 5000000) {
            $rules[] = [
                'rule' => 'high_amount',
                'message' => 'مبلغ كبير - يتطلب موافقة المدير المالي',
                'required_role' => 'financial_manager'
            ];
        } elseif ($custody->amount > 1000000) {
            $rules[] = [
                'rule' => 'medium_amount',
                'message' => 'مبلغ متوسط - يتطلب موافقة محاسب الإدارة',
                'required_role' => 'admin_accountant'
            ];
        }
        
        // قاعدة المشروع
        $project = $custody->project;
        if ($project && $project->budget_percentage >= 90) {
            $rules[] = [
                'rule' => 'project_budget_critical',
                'message' => 'المشروع وصل 90% من الميزانية - موافقة خاصة مطلوبة',
                'required_role' => 'financial_manager'
            ];
        }
        
        return $rules;
    }
    
    /**
     * حساب الحد الأقصى للعهدة
     */
    public function calculateMaxCustodyAmount($projectId, $locationId = null)
    {
        $project = \App\Models\Project::find($projectId);
        
        if (!$project) return 1000000; // حد أدنى مليون ريال
        
        // الحد الأقصى = 10% من إجمالي الميزانية (ليس المتبقية)
        $maxAmount = $project->total_budget * 0.1;
        
        // حد أدنى مليون وحد أقصى 5 مليون
        return max(min($maxAmount, 5000000), 1000000);
    }
    
    /**
     * إحصائيات العهد للمستخدم
     */
    public function getUserCustodyStats(User $user)
    {
        return [
            'total_custodies' => Custody::where('requested_by', $user->id)->count(),
            'open_custodies' => $this->getOpenCustodies($user)->count(),
            'overdue_custodies' => $this->getOverdueCustodies($user)->count(),
            'total_amount' => Custody::where('requested_by', $user->id)
                ->where('status', 'active')
                ->sum('amount'),
            'total_spent' => Custody::where('requested_by', $user->id)
                ->where('status', 'active')
                ->sum('spent_amount'),
            'can_request_new' => $this->canRequestNewCustody($user)['allowed']
        ];
    }
}

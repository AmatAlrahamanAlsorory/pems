<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Notification;
use App\Models\User;

class FraudDetectionService
{
    public function checkExpense(Expense $expense)
    {
        $issues = [];
        
        // فحص الفواتير المكررة
        if ($this->isDuplicateInvoice($expense)) {
            $issues[] = 'فاتورة مكررة';
        }
        
        // فحص المبالغ غير المعتادة
        if ($this->isUnusualAmount($expense)) {
            $issues[] = 'مبلغ غير معتاد';
        }
        
        // فحص التواريخ المشبوهة
        if ($this->isSuspiciousDate($expense)) {
            $issues[] = 'تاريخ مشبوه';
        }
        
        // فحص تكرار المورد
        if ($this->isFrequentVendor($expense)) {
            $issues[] = 'تكرار مشبوه للمورد';
        }
        
        if (!empty($issues)) {
            $this->createFraudAlert($expense, $issues);
        }
        
        return $issues;
    }
    
    private function isDuplicateInvoice(Expense $expense)
    {
        if (!$expense->invoice_number) return false;
        
        return Expense::where('invoice_number', $expense->invoice_number)
            ->where('id', '!=', $expense->id)
            ->where('vendor_name', $expense->vendor_name)
            ->exists();
    }
    
    private function isUnusualAmount(Expense $expense)
    {
        $stats = Expense::where('expense_category_id', $expense->expense_category_id)
            ->where('expense_item_id', $expense->expense_item_id)
            ->where('id', '!=', $expense->id)
            ->where('status', 'approved')
            ->selectRaw('AVG(amount) as avg, STDDEV(amount) as stddev')
            ->first();
            
        if (!$stats || !$stats->avg) return false;
        
        // إذا كان المبلغ أكبر من 3 انحرافات معيارية عن المتوسط
        $threshold = $stats->avg + (3 * ($stats->stddev ?? $stats->avg * 0.5));
        return $expense->amount > $threshold;
    }
    
    private function isSuspiciousDate(Expense $expense)
    {
        // فحص إذا كان تاريخ المصروف في المستقبل
        if ($expense->expense_date->isFuture()) {
            return true;
        }
        
        // فحص إذا كان التأخير في الإدخال أكثر من 30 يوم
        $daysLate = now()->diffInDays($expense->expense_date);
        if ($daysLate > 30) {
            return true;
        }
        
        return false;
    }
    
    private function isFrequentVendor(Expense $expense)
    {
        if (!$expense->vendor_name) return false;
        
        $count = Expense::where('vendor_name', $expense->vendor_name)
            ->where('created_by', $expense->created_by)
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->count();
            
        return $count > 10; // أكثر من 10 فواتير في أسبوع من نفس المورد
    }
    
    private function createFraudAlert(Expense $expense, array $issues)
    {
        $managers = User::whereIn('role', ['financial_manager', 'admin_accountant'])->get();
        
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => 'تحذير: مصروف مشبوه',
                'message' => "المصروف رقم {$expense->expense_number} يحتوي على: " . implode(', ', $issues),
                'type' => 'warning',
                'data' => json_encode([
                    'expense_id' => $expense->id,
                    'issues' => $issues,
                    'amount' => $expense->amount
                ])
            ]);
        }
    }
    
    public function generateFraudReport($dateFrom = null, $dateTo = null)
    {
        $query = Expense::with(['project', 'category', 'user']);
        
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $allExpenses = $query->get();
        $suspicious = [];
        
        foreach ($allExpenses as $expense) {
            $issues = $this->checkExpense($expense);
            if (!empty($issues)) {
                $suspicious[] = [
                    'expense' => $expense,
                    'issues' => $issues
                ];
            }
        }
        
        return [
            'total_checked' => $allExpenses->count(),
            'total_suspicious' => count($suspicious),
            'total_amount' => collect($suspicious)->sum('expense.amount'),
            'suspicious_expenses' => $suspicious,
            'risk_score' => $this->calculateRiskScore($suspicious, $allExpenses->count())
        ];
    }
    
    private function calculateRiskScore($suspicious, $total)
    {
        if ($total == 0) return 0;
        
        $percentage = (count($suspicious) / $total) * 100;
        
        if ($percentage > 10) return 'high';
        if ($percentage > 5) return 'medium';
        return 'low';
    }
}
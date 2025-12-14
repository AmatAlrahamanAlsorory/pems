<?php

namespace App\Services;

use App\Models\Project;
use App\Models\BudgetAllocation;
use App\Models\Expense;
use App\Models\Notification;
use App\Models\User;

class BudgetManagementService
{
    /**
     * إنشاء ميزانية فترية (أسبوعية/شهرية)
     */
    public function createPeriodicBudget($projectId, $period, $startDate, $endDate, $allocations)
    {
        $project = Project::findOrFail($projectId);
        
        $totalAllocated = collect($allocations)->sum('amount');
        
        if ($totalAllocated > $project->remaining_budget) {
            throw new \Exception('المبلغ المخصص يتجاوز الميزانية المتبقية');
        }
        
        $periodicBudget = \App\Models\PeriodicBudget::create([
            'project_id' => $projectId,
            'period_type' => $period, // weekly, monthly
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_budget' => $totalAllocated,
            'spent_amount' => 0,
            'status' => 'active'
        ]);
        
        foreach ($allocations as $allocation) {
            BudgetAllocation::create([
                'project_id' => $projectId,
                'expense_category_id' => $allocation['category_id'],
                'allocated_amount' => $allocation['amount'],
                'spent_amount' => 0,
                'period_budget_id' => $periodicBudget->id
            ]);
        }
        
        return $periodicBudget;
    }
    
    /**
     * تحويل ميزانية بين الفئات
     */
    public function transferBudget($fromCategoryId, $toCategoryId, $amount, $projectId, $reason)
    {
        $fromAllocation = BudgetAllocation::where('project_id', $projectId)
            ->where('expense_category_id', $fromCategoryId)
            ->firstOrFail();
            
        $toAllocation = BudgetAllocation::where('project_id', $projectId)
            ->where('expense_category_id', $toCategoryId)
            ->firstOrFail();
        
        // التحقق من الرصيد المتاح
        $availableAmount = $fromAllocation->allocated_amount - $fromAllocation->spent_amount;
        
        if ($amount > $availableAmount) {
            throw new \Exception('المبلغ المطلوب تحويله أكبر من المتاح');
        }
        
        // تنفيذ التحويل
        $fromAllocation->decrement('allocated_amount', $amount);
        $toAllocation->increment('allocated_amount', $amount);
        
        // تسجيل التحويل
        \App\Models\BudgetTransfer::create([
            'project_id' => $projectId,
            'from_category_id' => $fromCategoryId,
            'to_category_id' => $toCategoryId,
            'amount' => $amount,
            'reason' => $reason,
            'transferred_by' => auth()->id(),
            'transferred_at' => now()
        ]);
        
        // إشعار الإدارة
        $this->notifyBudgetTransfer($projectId, $fromCategoryId, $toCategoryId, $amount);
        
        return true;
    }
    
    /**
     * تعديل الميزانية
     */
    public function adjustBudget($projectId, $categoryId, $newAmount, $reason)
    {
        $allocation = BudgetAllocation::where('project_id', $projectId)
            ->where('expense_category_id', $categoryId)
            ->firstOrFail();
        
        $oldAmount = $allocation->allocated_amount;
        $difference = $newAmount - $oldAmount;
        
        $project = Project::findOrFail($projectId);
        
        // التحقق من الميزانية الكلية
        if ($difference > 0 && $difference > $project->remaining_budget) {
            throw new \Exception('الزيادة تتجاوز الميزانية المتبقية للمشروع');
        }
        
        $allocation->update(['allocated_amount' => $newAmount]);
        
        // تسجيل التعديل
        \App\Models\BudgetAdjustment::create([
            'project_id' => $projectId,
            'category_id' => $categoryId,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'difference' => $difference,
            'reason' => $reason,
            'adjusted_by' => auth()->id(),
            'adjusted_at' => now()
        ]);
        
        return true;
    }
    
    /**
     * حساب معدل الصرف اليومي
     */
    public function calculateDailyBurnRate($projectId, $days = 30)
    {
        $expenses = Expense::where('project_id', $projectId)
            ->where('status', 'approved')
            ->where('expense_date', '>=', now()->subDays($days))
            ->sum('amount');
        
        return $expenses / $days;
    }
    
    /**
     * توقع تاريخ نفاد الميزانية
     */
    public function predictBudgetDepletion($projectId)
    {
        $project = Project::findOrFail($projectId);
        $burnRate = $this->calculateDailyBurnRate($projectId);
        
        if ($burnRate == 0) {
            return null;
        }
        
        $daysRemaining = $project->remaining_budget / $burnRate;
        
        return [
            'days_remaining' => round($daysRemaining),
            'depletion_date' => now()->addDays($daysRemaining)->format('Y-m-d'),
            'daily_burn_rate' => $burnRate,
            'warning_level' => $daysRemaining < 30 ? 'critical' : ($daysRemaining < 60 ? 'warning' : 'normal')
        ];
    }
    
    /**
     * تحليل الميزانية حسب الفئة
     */
    public function analyzeBudgetByCategory($projectId)
    {
        $allocations = BudgetAllocation::with('category')
            ->where('project_id', $projectId)
            ->get();
        
        return $allocations->map(function($allocation) {
            $percentage = $allocation->allocated_amount > 0 
                ? ($allocation->spent_amount / $allocation->allocated_amount) * 100 
                : 0;
            
            return [
                'category' => $allocation->category->name,
                'allocated' => $allocation->allocated_amount,
                'spent' => $allocation->spent_amount,
                'remaining' => $allocation->allocated_amount - $allocation->spent_amount,
                'percentage' => round($percentage, 2),
                'status' => $percentage >= 90 ? 'critical' : ($percentage >= 70 ? 'warning' : 'normal')
            ];
        });
    }
    
    /**
     * مقارنة الميزانية المخططة مع الفعلية
     */
    public function comparePlannedVsActual($projectId)
    {
        $project = Project::with('budgetAllocations.category')->findOrFail($projectId);
        
        $comparison = [];
        
        foreach ($project->budgetAllocations as $allocation) {
            $actualSpent = Expense::where('project_id', $projectId)
                ->where('expense_category_id', $allocation->expense_category_id)
                ->where('status', 'approved')
                ->sum('amount');
            
            $variance = $actualSpent - $allocation->allocated_amount;
            $variancePercentage = $allocation->allocated_amount > 0 
                ? ($variance / $allocation->allocated_amount) * 100 
                : 0;
            
            $comparison[] = [
                'category' => $allocation->category->name,
                'planned' => $allocation->allocated_amount,
                'actual' => $actualSpent,
                'variance' => $variance,
                'variance_percentage' => round($variancePercentage, 2),
                'status' => $variance > 0 ? 'over' : 'under'
            ];
        }
        
        return $comparison;
    }
    
    /**
     * إشعار تحويل الميزانية
     */
    private function notifyBudgetTransfer($projectId, $fromCategoryId, $toCategoryId, $amount)
    {
        $managers = User::role(['financial_manager', 'admin_accountant'])->get();
        
        $fromCategory = \App\Models\ExpenseCategory::find($fromCategoryId);
        $toCategory = \App\Models\ExpenseCategory::find($toCategoryId);
        
        foreach ($managers as $manager) {
            Notification::create([
                'user_id' => $manager->id,
                'title' => 'تحويل ميزانية',
                'message' => "تم تحويل " . number_format($amount, 2) . " ريال من {$fromCategory->name} إلى {$toCategory->name}",
                'type' => 'info',
                'data' => json_encode([
                    'project_id' => $projectId,
                    'amount' => $amount
                ])
            ]);
        }
    }
    
    /**
     * تقرير الميزانية الشامل
     */
    public function generateBudgetReport($projectId)
    {
        $project = Project::with('budgetAllocations.category')->findOrFail($projectId);
        
        return [
            'project' => $project,
            'total_budget' => $project->total_budget,
            'spent_amount' => $project->spent_amount,
            'remaining_budget' => $project->remaining_budget,
            'budget_percentage' => $project->budget_percentage,
            'by_category' => $this->analyzeBudgetByCategory($projectId),
            'comparison' => $this->comparePlannedVsActual($projectId),
            'prediction' => $this->predictBudgetDepletion($projectId),
            'daily_burn_rate' => $this->calculateDailyBurnRate($projectId)
        ];
    }
}

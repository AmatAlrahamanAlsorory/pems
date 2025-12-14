<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Expense;
use App\Models\Custody;
use App\Models\User;

class ApprovalService
{
    public function requestApproval($item, User $requester)
    {
        $approvalLevel = $this->getRequiredApprovalLevel($item);
        
        if ($approvalLevel === 'automatic') {
            return $this->autoApprove($item, $requester);
        }

        return Approval::create([
            'approvable_type' => get_class($item),
            'approvable_id' => $item->id,
            'user_id' => $requester->id,
            'status' => 'pending',
        ]);
    }

    private function getRequiredApprovalLevel($item)
    {
        if ($item instanceof Expense) {
            return $item->item->approval_level ?? 'automatic';
        }
        
        if ($item instanceof Custody) {
            return $item->amount > 100000 ? 'management' : 'production_manager';
        }

        return 'automatic';
    }

    private function autoApprove($item, User $requester)
    {
        $approval = Approval::create([
            'approvable_type' => get_class($item),
            'approvable_id' => $item->id,
            'user_id' => $requester->id,
            'status' => 'approved',
            'approver_id' => $requester->id,
            'approved_at' => now(),
        ]);

        $this->processApproval($item);
        return $approval;
    }

    public function canApprove(User $user, $item)
    {
        if ($item instanceof Custody) {
            // محاسب الإدارة والمدير المالي يوافقان على جميع العهد
            return in_array($user->role, ['financial_manager', 'admin_accountant']);
        }
        
        $approvalLevel = $this->getRequiredApprovalLevel($item);
        
        return match($approvalLevel) {
            'automatic' => true,
            'production_manager' => in_array($user->role, ['financial_manager', 'production_manager']),
            'management' => in_array($user->role, ['financial_manager', 'admin_accountant']),
            default => false
        };
    }

    public function processApproval($item)
    {
        if ($item instanceof Expense) {
            $item->update(['status' => 'approved', 'approved_at' => now()]);
            
            // تحديث المبالغ
            $item->project->increment('spent_amount', $item->amount);
            
            $allocation = $item->project->budgetAllocations()
                ->where('expense_category_id', $item->expense_category_id)
                ->first();
            
            if ($allocation) {
                $allocation->increment('spent_amount', $item->amount);
            }
            
            if ($item->custody_id) {
                $item->custody->increment('spent_amount', $item->amount);
            }
        }
        
        if ($item instanceof Custody) {
            $item->update(['status' => 'approved', 'approved_at' => now()]);
        }
    }

    public function getPendingApprovals(User $user)
    {
        $approvals = collect();
        
        // العهد المطلوبة للموافقة
        $pendingCustodies = Custody::where('status', 'requested')
            ->with(['project', 'requestedBy'])
            ->get();
            
        foreach ($pendingCustodies as $custody) {
            if ($this->canApprove($user, $custody)) {
                $approvals->push((object)[
                    'id' => 'custody_' . $custody->id,
                    'approvable_type' => 'App\\Models\\Custody',
                    'approvable' => $custody,
                    'user' => $custody->requestedBy,
                    'created_at' => $custody->created_at,
                    'status' => 'pending'
                ]);
            }
        }
        
        // الموافقات العادية من جدول approvals
        $query = Approval::where('status', 'pending')->with(['approvable', 'user']);
        
        if ($user->role === 'production_manager') {
            $query->whereHasMorph('approvable', [Expense::class], function($q) {
                $q->whereHas('item', function($subQ) {
                    $subQ->where('approval_level', 'production_manager');
                });
            });
        } elseif (in_array($user->role, ['financial_manager', 'admin_accountant'])) {
            // يمكنهم رؤية جميع الموافقات
        } else {
            // لا شيء للمستخدمين العاديين
        }
        
        $regularApprovals = $query->latest()->get();
        $approvals = $approvals->merge($regularApprovals);
        
        return $approvals->sortByDesc('created_at');
    }
}
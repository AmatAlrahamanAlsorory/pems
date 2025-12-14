<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index()
    {
        $approvalService = app(ApprovalService::class);
        $pendingApprovals = $approvalService->getPendingApprovals(auth()->user());
        
        return view('approvals.index', compact('pendingApprovals'));
    }

    public function approve($approvalId, Request $request)
    {
        $approvalService = app(ApprovalService::class);
        
        // معالجة العهد
        if (str_starts_with($approvalId, 'custody_')) {
            $custodyId = str_replace('custody_', '', $approvalId);
            $custody = \App\Models\Custody::findOrFail($custodyId);
            
            if (!$approvalService->canApprove(auth()->user(), $custody)) {
                abort(403, 'غير مصرح لك بالموافقة على هذا الطلب');
            }
            
            $custody->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approval_date' => now()->toDateString()
            ]);
            
            return back()->with('success', 'تم الموافقة على العهدة بنجاح');
        }
        
        // معالجة الموافقات العادية
        $approval = Approval::findOrFail($approvalId);
        
        if (!$approvalService->canApprove(auth()->user(), $approval->approvable)) {
            abort(403, 'غير مصرح لك بالموافقة على هذا الطلب');
        }
        
        $approval->approve(auth()->user(), $request->notes);
        $approvalService->processApproval($approval->approvable);
        
        return back()->with('success', 'تم الموافقة على الطلب بنجاح');
    }

    public function reject($approvalId, Request $request)
    {
        $approvalService = app(ApprovalService::class);
        
        // معالجة العهد
        if (str_starts_with($approvalId, 'custody_')) {
            $custodyId = str_replace('custody_', '', $approvalId);
            $custody = \App\Models\Custody::findOrFail($custodyId);
            
            if (!$approvalService->canApprove(auth()->user(), $custody)) {
                abort(403, 'غير مصرح لك برفض هذا الطلب');
            }
            
            $custody->update([
                'status' => 'rejected',
                'notes' => ($custody->notes ?? '') . "\n\nسبب الرفض: " . ($request->notes ?? 'لم يتم تحديد سبب')
            ]);
            
            return back()->with('success', 'تم رفض العهدة');
        }
        
        // معالجة الموافقات العادية
        $approval = Approval::findOrFail($approvalId);
        
        if (!$approvalService->canApprove(auth()->user(), $approval->approvable)) {
            abort(403, 'غير مصرح لك برفض هذا الطلب');
        }
        
        $approval->reject(auth()->user(), $request->notes);
        $approval->approvable->update(['status' => 'rejected']);
        
        return back()->with('success', 'تم رفض الطلب');
    }
}
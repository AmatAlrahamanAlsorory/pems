<?php

namespace App\Http\Controllers;

use App\Models\Custody;
use App\Models\Project;
use Illuminate\Http\Request;

class CustodyController extends Controller
{
    public function index()
    {
        $custodies = Custody::with(['project', 'requestedBy'])
            ->latest()
            ->paginate(15);
        
        return view('custodies.index', compact('custodies'));
    }

    public function create()
    {
        // فحص الصلاحيات
        if (!auth()->user()->hasPermission('create_custody')) {
            abort(403, 'غير مصرح لك بإنشاء عهدة جديدة');
        }
        
        $projects = Project::all();
        $custodyRules = app(\App\Services\CustodyRulesService::class);
        $stats = $custodyRules->getUserCustodyStats(auth()->user());
        
        return view('custodies.create', compact('projects', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:1000',
            'currency' => 'required|in:YER,SAR,USD',
            'purpose' => 'required|string|min:10',
            'notes' => 'nullable|string',
        ]);

        // تطبيق قواعد العهد الصارمة
        $custodyRules = app(\App\Services\CustodyRulesService::class);
        $canRequest = $custodyRules->canRequestNewCustody(auth()->user(), $validated['project_id']);
        
        if (!$canRequest['allowed']) {
            return back()->withErrors($canRequest['errors'])->withInput();
        }
        
        // فحص الحد الأقصى
        $maxAmount = $custodyRules->calculateMaxCustodyAmount($validated['project_id']);
        if ($validated['amount'] > $maxAmount) {
            return back()->withErrors(['amount' => "الحد الأقصى للعهدة: " . number_format($maxAmount) . " ريال"])->withInput();
        }

        $location = \App\Models\Location::firstOrCreate(
            ['project_id' => $validated['project_id'], 'name' => 'الموقع الرئيسي'],
            ['city' => 'صنعاء', 'status' => 'active']
        );

        $custody = Custody::create([
            'custody_number' => 'C' . date('Ymd') . str_pad(Custody::count() + 1, 4, '0', STR_PAD_LEFT),
            'project_id' => $validated['project_id'],
            'location_id' => $location->id,
            'requested_by' => auth()->id(),
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'remaining_amount' => $validated['amount'],
            'status' => 'requested',
            'request_date' => now()->toDateString(),
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'],
        ]);

        // إشعار للمحاسب الإداري
        $adminAccountant = \App\Models\User::where('role', 'admin_accountant')->first();
        if ($adminAccountant) {
            \App\Models\Notification::create([
                'user_id' => $adminAccountant->id,
                'title' => 'طلب عهدة جديد',
                'message' => "طلب عهدة جديد من {$custody->requestedBy->name} بمبلغ " . number_format($custody->amount) . " {$custody->currency}",
                'type' => 'custody_request',
                'level' => 'info',
                'data' => json_encode(['custody_id' => $custody->id])
            ]);
        }
        
        return redirect()->route('custodies.index')
            ->with('success', 'تم إنشاء طلب العهدة بنجاح. في انتظار الموافقة.');
    }

    public function show(Custody $custody)
    {
        $custody->load(['project', 'requestedBy', 'approvedBy', 'expenses']);
        return view('custodies.show', compact('custody'));
    }

    public function approve(Custody $custody)
    {
        // تطبيق قواعد الموافقة
        $custodyRules = app(\App\Services\CustodyRulesService::class);
        $rules = $custodyRules->applyApprovalRules($custody);
        
        // فحص الصلاحيات
        foreach ($rules as $rule) {
            if (isset($rule['required_role']) && !auth()->user()->hasRole($rule['required_role'])) {
                return back()->withErrors(['approval' => $rule['message']]);
            }
        }
        
        $custody->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        
        // تسجيل في Audit Log
        app(\App\Services\AuditLogService::class)->logApproval($custody, 'approved');
        
        return back()->with('success', 'تم الموافقة على العهدة');
    }

    public function settle(Request $request, Custody $custody)
    {
        $validated = $request->validate([
            'settlement_notes' => 'nullable|string',
            'actual_spent' => 'required|numeric|min:0'
        ]);
        
        $custody->update([
            'status' => 'settled',
            'settled_at' => now(),
            'spent_amount' => $validated['actual_spent'],
            'remaining_amount' => $custody->amount - $validated['actual_spent'],
            'notes' => ($custody->notes ?? '') . "\n\nتصفية: " . ($validated['settlement_notes'] ?? '')
        ]);
        
        app(\App\Services\AuditLogService::class)->log('custody_settled', $custody->id, [
            'custody_number' => $custody->custody_number,
            'amount' => $custody->amount,
            'spent' => $validated['actual_spent']
        ]);
        
        return back()->with('success', 'تم تصفية العهدة بنجاح');
    }
    
    public function stats()
    {
        $custodyRules = app(\App\Services\CustodyRulesService::class);
        $stats = $custodyRules->getUserCustodyStats(auth()->user());
        
        return response()->json($stats);
    }
}

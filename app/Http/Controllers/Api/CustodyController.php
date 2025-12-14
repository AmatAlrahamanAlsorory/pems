<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Custody;
use App\Services\ApprovalService;
use Illuminate\Http\Request;

class CustodyController extends Controller
{
    public function index(Request $request)
    {
        $custodies = Custody::with(['project', 'user', 'latestApproval'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($custodies);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0',
            'purpose' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        $custody = Custody::create($validated);
        
        $approvalService = app(ApprovalService::class);
        $approval = $approvalService->requestApproval($custody, $request->user());
        
        return response()->json([
            'custody' => $custody->load('project'),
            'approval_status' => $approval->status,
            'message' => $approval->status === 'approved' ? 
                'تم إنشاء واعتماد العهدة بنجاح' : 
                'تم إنشاء العهدة وإرسالها للموافقة'
        ], 201);
    }
}
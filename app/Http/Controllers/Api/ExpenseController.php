<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Services\ApprovalService;
use App\Services\OCRService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with(['project', 'category', 'item', 'person', 'latestApproval'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'custody_id' => 'nullable|exists:custodies,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_item_id' => 'required|exists:expense_items,id',
            'person_id' => 'nullable|exists:people,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        $expense = Expense::create($validated);
        
        $approvalService = app(ApprovalService::class);
        $approval = $approvalService->requestApproval($expense, $request->user());
        
        return response()->json([
            'expense' => $expense->load(['project', 'category', 'item']),
            'approval_status' => $approval->status,
            'message' => $approval->status === 'approved' ? 
                'تم تسجيل واعتماد المصروف بنجاح' : 
                'تم تسجيل المصروف وإرساله للموافقة'
        ], 201);
    }

    public function uploadInvoice(Request $request, Expense $expense)
    {
        $request->validate(['invoice' => 'required|image|max:5120']);
        
        if ($expense->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $ocrService = app(OCRService::class);
        $result = $ocrService->extractInvoiceData($request->file('invoice'));
        
        $expense->update(['invoice_file' => $result['file_path']]);
        
        return response()->json([
            'message' => 'تم رفع الفاتورة بنجاح',
            'extracted_data' => $result['extracted_data'],
            'confidence' => $result['confidence']
        ]);
    }
}
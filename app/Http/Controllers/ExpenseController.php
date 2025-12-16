<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use App\Models\Custody;
use App\Models\ExpenseCategory;
use App\Services\AlertService;
use App\Services\OCRService;
use App\Services\FraudDetectionService;
use App\Services\FileEncryptionService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['project', 'category', 'custody', 'user'])
            ->latest()
            ->paginate(20);
        
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        // فحص الصلاحيات
        if (!auth()->user()->hasPermission('create_expense')) {
            abort(403, 'غير مصرح لك بإنشاء مصروف جديد');
        }
        
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $items = \App\Models\ExpenseItem::where('is_active', true)->get();
        $custodies = Custody::with(['project', 'requestedBy'])->get();
        $people = \App\Models\Person::where('is_active', true)->get();
        
        return view('expenses.create', compact('projects', 'categories', 'items', 'custodies', 'people'));
    }

    public function store(Request $request)
    {
        // فحص الصلاحيات
        if (!auth()->user()->hasPermission('create_expense')) {
            abort(403, 'غير مصرح لك بإنشاء مصروف جديد');
        }
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'custody_id' => 'nullable|exists:custodies,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_item_id' => 'required|exists:expense_items,id',
            'person_id' => 'nullable|exists:people,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:YER,SAR,USD',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string',
            'invoice_file' => 'nullable|image|max:5120',
            'notes' => 'nullable|string',
        ]);
        
        // حفظ الفاتورة بشكل بسيط
        if ($request->hasFile('invoice_file')) {
            $validated['invoice_file'] = $request->file('invoice_file')->store('invoices', 'public');
        }

        // فحص بسيط للميزانية
        $project = Project::find($validated['project_id']);
        $newTotal = $project->spent_amount + $validated['amount'];
        if ($newTotal > $project->total_budget) {
            return back()->withErrors(['amount' => 'هذا المبلغ سيتجاوز ميزانية المشروع'])->withInput();
        }

        // الحصول أو إنشاء موقع
        $location = \App\Models\Location::firstOrCreate(
            ['project_id' => $validated['project_id']],
            ['name' => 'الموقع الرئيسي', 'city' => 'صنعاء', 'status' => 'active']
        );

        // إنشاء المصروف
        $expense = Expense::create([
            'expense_number' => 'EXP-' . now()->format('Ymd') . '-' . uniqid(),
            'project_id' => $validated['project_id'],
            'location_id' => $location->id,
            'custody_id' => $validated['custody_id'],
            'expense_category_id' => $validated['expense_category_id'],
            'expense_item_id' => $validated['expense_item_id'],
            'person_id' => $validated['person_id'],
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'description' => $validated['description'],
            'expense_date' => $validated['expense_date'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_file' => $validated['invoice_file'] ?? null,
            'status' => 'confirmed',
            'created_by' => auth()->id(),
        ]);

        // تحديث المشروع
        $project->increment('spent_amount', $validated['amount']);

        return redirect()->route('expenses.index')->with('success', 'تم حفظ المصروف بنجاح');
    }

    public function show(Expense $expense)
    {
        $expense->load(['project', 'category', 'item', 'custody', 'user', 'person']);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $projects = Project::all();
        $categories = ExpenseCategory::all();
        $items = \App\Models\ExpenseItem::where('is_active', true)->get();
        $custodies = Custody::with(['project', 'requestedBy'])->get();
        $people = \App\Models\Person::where('is_active', true)->get();
        
        return view('expenses.edit', compact('expense', 'projects', 'categories', 'items', 'custodies', 'people'));
    }
    
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'custody_id' => 'nullable|exists:custodies,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_item_id' => 'required|exists:expense_items,id',
            'person_id' => 'nullable|exists:people,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|in:YER,SAR,USD',
            'description' => 'required|string',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        $expense->update($validated);
        
        return redirect()->route('expenses.index')
            ->with('success', 'تم تحديث المصروف بنجاح');
    }
    
    public function destroy(Expense $expense)
    {
        $expense->delete();
        
        return redirect()->route('expenses.index')
            ->with('success', 'تم حذف المصروف بنجاح');
    }
    
    public function approve(Expense $expense)
    {
        $approvalService = app(\App\Services\ApprovalService::class);
        
        if (!$approvalService->canApprove(auth()->user(), $expense)) {
            abort(403, 'غير مصرح لك بالموافقة على هذا المصروف');
        }
        
        $approval = $expense->latestApproval;
        if ($approval && $approval->status === 'pending') {
            $approval->approve(auth()->user());
            $approvalService->processApproval($expense);
        }
        
        return back()->with('success', 'تم الموافقة على المصروف');
    }
}

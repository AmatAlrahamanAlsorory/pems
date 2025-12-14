@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">إنشاء ميزانية دورية جديدة</h1>
            <p class="text-gray-600">مشروع: {{ $project->name }}</p>
        </div>

        <form action="{{ route('periodic-budgets.store', $project) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">معلومات الميزانية</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع الفترة</label>
                        <select name="period_type" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">اختر نوع الفترة</option>
                            <option value="weekly">أسبوعية</option>
                            <option value="monthly">شهرية</option>
                            <option value="quarterly">ربع سنوية</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">إجمالي الميزانية</label>
                        <input type="number" name="total_budget" step="0.01" min="0" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ البداية</label>
                        <input type="date" name="start_date" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ النهاية</label>
                        <input type="date" name="end_date" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">توزيع الميزانية على الفئات</h3>
                
                <div id="allocations-container">
                    @foreach($categories as $index => $category)
                    <div class="allocation-row grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded mr-2" style="background-color: {{ $category->color }}"></div>
                                <span class="text-sm">{{ $category->name }}</span>
                                <input type="hidden" name="allocations[{{ $index }}][category_id]" value="{{ $category->id }}">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ المخصص</label>
                            <input type="number" name="allocations[{{ $index }}][amount]" step="0.01" min="0" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 allocation-amount" 
                                   placeholder="0.00">
                        </div>
                        
                        <div class="flex items-end">
                            <span class="text-sm text-gray-500 allocation-percentage">0%</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">إجمالي التوزيعات:</span>
                        <span id="total-allocations" class="font-bold text-lg">0.00 ريال</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm text-gray-600">المتبقي:</span>
                        <span id="remaining-budget" class="text-sm font-medium">0.00 ريال</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('periodic-budgets.index', $project) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    إنشاء الميزانية
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalBudgetInput = document.querySelector('input[name="total_budget"]');
    const allocationAmounts = document.querySelectorAll('.allocation-amount');
    const totalAllocationsSpan = document.getElementById('total-allocations');
    const remainingBudgetSpan = document.getElementById('remaining-budget');
    
    function updateTotals() {
        const totalBudget = parseFloat(totalBudgetInput.value) || 0;
        let totalAllocations = 0;
        
        allocationAmounts.forEach((input, index) => {
            const amount = parseFloat(input.value) || 0;
            totalAllocations += amount;
            
            // تحديث النسبة المئوية
            const percentage = totalBudget > 0 ? (amount / totalBudget * 100).toFixed(1) : 0;
            const percentageSpan = input.closest('.allocation-row').querySelector('.allocation-percentage');
            percentageSpan.textContent = percentage + '%';
        });
        
        const remaining = totalBudget - totalAllocations;
        
        totalAllocationsSpan.textContent = totalAllocations.toLocaleString('ar-SA', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ريال';
        
        remainingBudgetSpan.textContent = remaining.toLocaleString('ar-SA', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ريال';
        
        // تغيير لون المتبقي حسب الحالة
        if (remaining < 0) {
            remainingBudgetSpan.className = 'text-sm font-medium text-red-600';
        } else if (remaining === 0) {
            remainingBudgetSpan.className = 'text-sm font-medium text-green-600';
        } else {
            remainingBudgetSpan.className = 'text-sm font-medium text-gray-900';
        }
    }
    
    totalBudgetInput.addEventListener('input', updateTotals);
    allocationAmounts.forEach(input => {
        input.addEventListener('input', updateTotals);
    });
});
</script>
@endsection
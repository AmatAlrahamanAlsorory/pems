@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">تقرير كشف الاحتيال</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form id="fraud-form" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-2">من تاريخ</label>
                <input type="date" name="date_from" class="w-full px-3 py-2 border rounded">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">إلى تاريخ</label>
                <input type="date" name="date_to" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded font-semibold">
                    تحليل
                </button>
            </div>
        </form>
    </div>

    <div id="fraud-results" class="hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm mb-1">إجمالي المصروفات المفحوصة</p>
                <p id="total-checked" class="text-3xl font-bold">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm mb-1">المصروفات المشبوهة</p>
                <p id="total-suspicious" class="text-3xl font-bold text-red-600">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm mb-1">مستوى الخطر</p>
                <p id="risk-score" class="text-3xl font-bold">-</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-bold">المصروفات المشبوهة</h3>
            </div>
            <div id="suspicious-list" class="divide-y"></div>
        </div>
    </div>
</div>

<script>
document.getElementById('fraud-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);
    
    fetch(`/api/ai/fraud?${params}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('fraud-results').classList.remove('hidden');
            document.getElementById('total-checked').textContent = data.total_checked;
            document.getElementById('total-suspicious').textContent = data.total_suspicious;
            
            const riskEl = document.getElementById('risk-score');
            riskEl.textContent = data.risk_score === 'high' ? 'عالي' : data.risk_score === 'medium' ? 'متوسط' : 'منخفض';
            riskEl.className = `text-3xl font-bold ${
                data.risk_score === 'high' ? 'text-red-600' : 
                data.risk_score === 'medium' ? 'text-orange-600' : 
                'text-green-600'
            }`;
            
            const list = document.getElementById('suspicious-list');
            if (data.suspicious_expenses.length === 0) {
                list.innerHTML = '<div class="p-6 text-center text-gray-500">لا توجد مصروفات مشبوهة</div>';
            } else {
                list.innerHTML = data.suspicious_expenses.map(item => `
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">${item.expense.expense_number}</p>
                                <p class="text-sm text-gray-600">${item.expense.description || 'بدون وصف'}</p>
                                <div class="flex gap-2 mt-2">
                                    ${item.issues.map(issue => `<span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">${issue}</span>`).join('')}
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-bold">${Number(item.expense.amount).toLocaleString()} ر.س</p>
                                <p class="text-sm text-gray-600">${item.expense.expense_date}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(err => {
            alert('حدث خطأ في التحليل');
        });
});
</script>
@endsection

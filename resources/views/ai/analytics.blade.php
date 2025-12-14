@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">التحليلات التنبؤية</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($projects as $project)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">{{ $project->name }}</h3>
            
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span>الميزانية المصروفة</span>
                    <span class="font-bold">{{ number_format($project->budget_percentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($project->budget_percentage, 100) }}%"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                <div>
                    <p class="text-gray-600">الميزانية الكلية</p>
                    <p class="font-bold">{{ number_format($project->total_budget) }} ر.س</p>
                </div>
                <div>
                    <p class="text-gray-600">المصروف</p>
                    <p class="font-bold">{{ number_format($project->spent_amount) }} ر.س</p>
                </div>
                <div>
                    <p class="text-gray-600">المتبقي</p>
                    <p class="font-bold">{{ number_format($project->remaining_budget) }} ر.س</p>
                </div>
                <div>
                    <p class="text-gray-600">الحالة</p>
                    <p class="font-bold">
                        @if($project->budget_percentage >= 100)
                            <span class="text-red-600">تجاوز</span>
                        @elseif($project->budget_percentage >= 90)
                            <span class="text-orange-600">حرج</span>
                        @elseif($project->budget_percentage >= 70)
                            <span class="text-yellow-600">تحذير</span>
                        @else
                            <span class="text-green-600">آمن</span>
                        @endif
                    </p>
                </div>
            </div>

            <button 
                onclick="loadPrediction({{ $project->id }})"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded font-semibold">
                عرض التنبؤات
            </button>

            <div id="prediction-{{ $project->id }}" class="mt-4 hidden"></div>
        </div>
        @endforeach
    </div>
</div>

<script>
function loadPrediction(projectId) {
    const container = document.getElementById(`prediction-${projectId}`);
    container.innerHTML = '<div class="text-center py-4"><div class="spinner"></div></div>';
    container.classList.remove('hidden');
    
    fetch(`/api/ai/predict/${projectId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'insufficient_data') {
                container.innerHTML = `<div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-sm">${data.message}</div>`;
                return;
            }
            
            container.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded p-4">
                    <h4 class="font-bold mb-2">التنبؤ المالي</h4>
                    <div class="text-sm space-y-2">
                        <p><strong>المصروف المتوقع:</strong> ${Number(data.predicted_total).toLocaleString()} ر.س</p>
                        <p><strong>احتمال التجاوز:</strong> ${data.overrun_risk ? 'نعم ⚠️' : 'لا ✓'}</p>
                        ${data.overrun_amount > 0 ? `<p class="text-red-600"><strong>مبلغ التجاوز المتوقع:</strong> ${Number(data.overrun_amount).toLocaleString()} ر.س</p>` : ''}
                        <p><strong>مستوى الثقة:</strong> ${data.confidence}%</p>
                        <p class="mt-3 p-2 bg-white rounded"><strong>التوصية:</strong> ${data.recommendation}</p>
                    </div>
                </div>
            `;
        })
        .catch(err => {
            container.innerHTML = '<div class="bg-red-50 border border-red-200 rounded p-4 text-sm text-red-800">حدث خطأ في التحليل</div>';
        });
}
</script>
@endsection

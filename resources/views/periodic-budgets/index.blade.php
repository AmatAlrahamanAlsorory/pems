@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الميزانيات الدورية</h1>
            <p class="text-gray-600">مشروع: {{ $project->name }}</p>
        </div>
        <a href="{{ route('periodic-budgets.create', $project) }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            إنشاء ميزانية دورية جديدة
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">إجمالي الميزانيات</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $periodicBudgets->sum('total_budget') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">المصروف</h3>
            <p class="text-3xl font-bold text-green-600">{{ $periodicBudgets->sum('spent_amount') }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">المتبقي</h3>
            <p class="text-3xl font-bold text-orange-600">{{ $periodicBudgets->sum('total_budget') - $periodicBudgets->sum('spent_amount') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الفترة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الميزانية</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المصروف</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النسبة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($periodicBudgets as $budget)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($budget->period_type === 'weekly') bg-blue-100 text-blue-800
                            @elseif($budget->period_type === 'monthly') bg-green-100 text-green-800
                            @else bg-purple-100 text-purple-800 @endif">
                            {{ $budget->period_type === 'weekly' ? 'أسبوعية' : ($budget->period_type === 'monthly' ? 'شهرية' : 'ربع سنوية') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $budget->start_date->format('Y-m-d') }} - {{ $budget->end_date->format('Y-m-d') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ number_format($budget->total_budget, 2) }} ريال
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($budget->spent_amount, 2) }} ريال
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="h-2 rounded-full 
                                    @if($budget->budget_percentage >= 90) bg-red-600
                                    @elseif($budget->budget_percentage >= 70) bg-yellow-600
                                    @else bg-green-600 @endif" 
                                    style="width: {{ min($budget->budget_percentage, 100) }}%"></div>
                            </div>
                            <span class="text-sm text-gray-900">{{ number_format($budget->budget_percentage, 1) }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            @if($budget->status === 'active') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $budget->status === 'active' ? 'نشطة' : 'منتهية' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('periodic-budgets.show', [$project, $budget]) }}" 
                           class="text-blue-600 hover:text-blue-900">عرض</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        لا توجد ميزانيات دورية
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $periodicBudgets->links() }}
    </div>
</div>
@endsection
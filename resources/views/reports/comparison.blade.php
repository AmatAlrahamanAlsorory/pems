<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            تقرير المقارنة - {{ $project->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- فلتر المشروع -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <form method="GET" class="flex items-center gap-4">
                    <select name="project_id" class="border border-gray-300 rounded-lg px-4 py-2" onchange="this.form.submit()">
                        <option value="">اختر المشروع</option>
                        @foreach(\App\Models\Project::all() as $proj)
                            <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            @if(isset($project))
            <!-- ملخص المشروع -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">الميزانية المخططة</p>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($project->total_budget) }} ر.س</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">المصروف الفعلي</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($project->spent_amount) }} ر.س</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">الفرق</p>
                        @php $diff = $project->spent_amount - $project->total_budget; @endphp
                        <p class="text-2xl font-bold {{ $diff > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $diff > 0 ? '+' : '' }}{{ number_format($diff) }} ر.س
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">نسبة الإنفاق</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($project->budget_percentage, 1) }}%</p>
                    </div>
                </div>
            </div>

            <!-- مقارنة الفئات -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6">مقارنة الفئات - مخطط مقابل فعلي</h3>
                
                <div class="space-y-4">
                    @foreach($project->budgetAllocations as $allocation)
                        @php
                            $actualSpent = $actualSpending->where('name', $allocation->category->name)->first();
                            $actualAmount = $actualSpent ? $actualSpent->actual_amount : 0;
                            $plannedAmount = $allocation->allocated_amount;
                            $variance = $actualAmount - $plannedAmount;
                            $variancePercent = $plannedAmount > 0 ? ($variance / $plannedAmount) * 100 : 0;
                        @endphp
                        
                        <div class="border rounded-lg p-4" style="border-color: {{ $allocation->category->color }}">
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded mr-3" style="background-color: {{ $allocation->category->color }}"></div>
                                    <h4 class="font-bold text-gray-900">{{ $allocation->category->name }}</h4>
                                </div>
                                <div class="text-sm {{ $variance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $variance > 0 ? '+' : '' }}{{ number_format($variancePercent, 1) }}%
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-3 gap-4 mb-3">
                                <div>
                                    <p class="text-xs text-gray-600">مخطط</p>
                                    <p class="font-bold text-blue-600">{{ number_format($plannedAmount) }} ر.س</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">فعلي</p>
                                    <p class="font-bold text-red-600">{{ number_format($actualAmount) }} ر.س</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">الفرق</p>
                                    <p class="font-bold {{ $variance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $variance > 0 ? '+' : '' }}{{ number_format($variance) }} ر.س
                                    </p>
                                </div>
                            </div>
                            
                            <!-- شريط المقارنة -->
                            <div class="relative">
                                <div class="flex h-6 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="bg-blue-500 flex items-center justify-center text-xs text-white font-bold"
                                         style="width: {{ $plannedAmount > 0 ? min(($plannedAmount / max($plannedAmount, $actualAmount)) * 100, 100) : 0 }}%">
                                        مخطط
                                    </div>
                                    <div class="bg-red-500 flex items-center justify-center text-xs text-white font-bold"
                                         style="width: {{ $actualAmount > 0 ? min(($actualAmount / max($plannedAmount, $actualAmount)) * 100, 100) : 0 }}%">
                                        فعلي
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
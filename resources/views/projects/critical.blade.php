<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-red-600">المشاريع الحرجة</h2>
                <p class="text-gray-600">المشاريع التي تجاوزت 90% من الميزانية</p>
            </div>

            <div class="grid gap-4">
                @forelse($criticalProjects as $project)
                    <div class="card p-4 border-l-4 
                        @if($project->budget_status === 'critical') border-red-500 bg-red-50
                        @else border-orange-500 bg-orange-50 @endif">
                        
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $project->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $project->type }}</p>
                            </div>
                            
                            <div class="text-right">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    @if($project->budget_status === 'critical') bg-red-100 text-red-800
                                    @else bg-orange-100 text-orange-800 @endif">
                                    {{ $project->budget_status === 'critical' ? 'حرج' : 'خطر' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">الميزانية الكلية:</span>
                                <p class="font-medium">{{ number_format($project->total_budget) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">المصروف:</span>
                                <p class="font-medium text-red-600">{{ number_format($project->spent_amount) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">المتبقي:</span>
                                <p class="font-medium">{{ number_format($project->remaining_budget) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">النسبة:</span>
                                <p class="font-bold text-red-600">{{ number_format($project->budget_percentage, 1) }}%</p>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full 
                                    @if($project->budget_percentage >= 100) bg-red-600
                                    @else bg-orange-500 @endif" 
                                    style="width: {{ min($project->budget_percentage, 100) }}%"></div>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="btn-primary text-xs">عرض التفاصيل</a>
                            <a href="{{ route('expenses.index', ['project_id' => $project->id]) }}" class="btn-secondary text-xs">المصروفات</a>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center">
                        <div class="text-green-600 text-4xl mb-4">✓</div>
                        <h3 class="text-lg font-medium text-gray-900">لا توجد مشاريع حرجة</h3>
                        <p class="text-gray-500">جميع المشاريع ضمن الحدود الآمنة للميزانية</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
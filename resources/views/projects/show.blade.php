<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $project->name }}</h2>
                    <p class="text-gray-600 text-sm">تفاصيل المشروع</p>
                </div>
                <div class="flex gap-2">
                    @permission('edit_project')
                    <a href="{{ route('projects.edit', $project) }}" class="btn-primary">
                        تعديل
                    </a>
                    @endpermission
                    <a href="{{ route('projects.index') }}" class="btn-secondary">
                        رجوع
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Project Details -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                            <h3 class="text-xl font-bold">معلومات المشروع</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">النوع</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    @if($project->type == 'series') مسلسل
                                    @elseif($project->type == 'movie') فيلم
                                    @else برنامج @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">الحالة</p>
                                <p class="text-lg font-semibold">
                                    @php
                                        $statusNames = ['planning' => 'تخطيط', 'active' => 'نشط', 'on_hold' => 'متوقف', 'completed' => 'مكتمل', 'cancelled' => 'ملغي'];
                                    @endphp
                                    {{ $statusNames[$project->status] }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">تاريخ البدء</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $project->start_date->format('Y/m/d') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">تاريخ الانتهاء</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $project->end_date?->format('Y/m/d') ?? 'غير محدد' }}</p>
                            </div>
                            @if($project->planned_days)
                            <div>
                                <p class="text-sm text-gray-600">أيام التصوير</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $project->planned_days }} يوم</p>
                            </div>
                            @endif
                            @if($project->episodes_count)
                            <div>
                                <p class="text-sm text-gray-600">عدد الحلقات</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $project->episodes_count }} حلقة</p>
                            </div>
                            @endif
                        </div>
                        @if($project->description)
                        <div class="px-6 pb-6">
                            <p class="text-sm text-gray-600 mb-2">الوصف</p>
                            <p class="text-gray-700">{{ $project->description }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Expenses List -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white flex justify-between items-center">
                            <h3 class="text-xl font-bold">المصروفات الأخيرة</h3>
                            @permission('create_expense')
                            <a href="{{ route('expenses.create') }}" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-sm transition">
                                + إضافة مصروف
                            </a>
                            @endpermission
                        </div>
                        <div class="p-6">
                            @forelse($project->expenses->take(5) as $expense)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $expense->category->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $expense->expense_date->format('Y/m/d') }}</p>
                                    </div>
                                    <p class="text-lg font-bold text-blue-600">{{ number_format($expense->amount) }} ر.س</p>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 py-8">لا توجد مصروفات</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Budget Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-green-600 to-green-700 p-6 text-white flex justify-between items-center">
                            <h3 class="text-xl font-bold">الميزانية</h3>
                            @can('manage-projects')
                                <a href="{{ route('budget-allocations.create', $project) }}" class="bg-white/20 hover:bg-white/30 px-3 py-1 rounded text-sm transition">
                                    توزيع
                                </a>
                            @endcan
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">الميزانية الكلية</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($project->total_budget) }} ر.س</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">المصروف</p>
                                <p class="text-2xl font-bold text-red-600">{{ number_format($project->spent_amount) }} ر.س</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">المتبقي</p>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($project->remaining_budget) }} ر.س</p>
                            </div>
                            <div class="pt-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">نسبة الإنفاق</span>
                                    <span class="font-bold text-gray-900">{{ number_format($project->budget_percentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full h-3 transition-all" 
                                         style="width: {{ min($project->budget_percentage, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-purple-600 to-purple-700 p-6 text-white">
                            <h3 class="text-xl font-bold">الإحصائيات</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">عدد المصروفات</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $project->expenses->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">عدد العهد</span>
                                <span class="text-2xl font-bold text-gray-900">{{ $project->custodies->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            التحليلات التنبؤية
        </h2>
    </x-slot>
    
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/analytics.css') }}">
    @endpush

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- مقدمة عن التحليلات التنبؤية -->
            <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 rounded-2xl shadow-sm border border-blue-100 p-8 mb-8">
                <div class="text-center max-w-4xl mx-auto">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        <h3 class="text-3xl font-bold mb-3">التحليلات التنبؤية المتقدمة</h3>
                    </div>
                    <p class="text-gray-600 mb-8 text-lg">
                        استخدم قوة الذكاء الاصطناعي لتحسين إدارة الميزانية
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">توقع المصروفات</h4>
                            <p class="text-sm text-gray-600">تنبؤات ذكية للإنفاق المستقبلي</p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                            <div class="bg-yellow-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">تحليل المخاطر</h4>
                            <p class="text-sm text-gray-600">رصد مخاطر تجاوز الميزانية</p>
                        </div>
                        
                        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                            <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">كفاءة الإنفاق</h4>
                            <p class="text-sm text-gray-600">تحليل وتحسين الأداء المالي</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- قائمة المشاريع -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">اختر مشروعاً لعرض التحليلات التنبؤية</h3>
                    <p class="text-sm text-gray-600 mt-1">انقر على أي مشروع لعرض التحليلات التنبؤية المفصلة</p>
                </div>

                <div class="p-6">
                    @if($projects->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($projects as $project)
                                <div class="bg-gray-50 rounded-lg p-6 hover:bg-gray-100 transition-colors duration-200 cursor-pointer border border-gray-200 hover:border-blue-300"
                                     onclick="window.location.href='{{ route('analytics.predictive', $project->id) }}'">
                                    
                                    <!-- رأس البطاقة -->
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $project->name }}</h4>
                                            <p class="text-sm text-gray-600 line-clamp-2">{{ $project->description }}</p>
                                        </div>
                                        <div class="mr-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                   ($project->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $project->status === 'active' ? 'نشط' : 
                                                   ($project->status === 'completed' ? 'مكتمل' : 'متوقف') }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- إحصائيات المشروع -->
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">الميزانية الإجمالية:</span>
                                            <span class="font-medium text-gray-900">
                                                {{ number_format($project->total_budget) }} ر.س
                                            </span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">المصروف:</span>
                                            <span class="font-medium text-gray-900">
                                                {{ number_format($project->spent_amount) }} ر.س
                                            </span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-600">المتبقي:</span>
                                            <span class="font-medium text-gray-900">
                                                {{ number_format($project->remaining_budget) }} ر.س
                                            </span>
                                        </div>

                                        <!-- شريط التقدم -->
                                        @php
                                            $progress = $project->total_budget > 0 ? ($project->spent_amount / $project->total_budget) * 100 : 0;
                                        @endphp
                                        <div class="mt-4">
                                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                                <span>نسبة الإنفاق</span>
                                                <span>{{ number_format($progress, 1) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $progress >= 90 ? 'bg-red-500' : ($progress >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                                     style="width: {{ min($progress, 100) }}%"></div>
                                            </div>
                                        </div>

                                        <!-- عدد المصروفات -->
                                        <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                            <span class="text-sm text-gray-600">عدد المصروفات:</span>
                                            <span class="font-medium text-gray-900">
                                                {{ $project->expenses_count ?? 0 }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- زر العرض -->
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <div class="flex items-center justify-center text-blue-600 hover:text-blue-800 font-medium">
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            عرض التحليلات التنبؤية
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد مشاريع</h3>
                            <p class="mt-1 text-sm text-gray-500">ابدأ بإنشاء مشروع جديد لعرض التحليلات التنبؤية</p>
                            @permission('create_project')
                                <div class="mt-6">
                                    <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        إنشاء مشروع جديد
                                    </a>
                                </div>
                            @endpermission
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
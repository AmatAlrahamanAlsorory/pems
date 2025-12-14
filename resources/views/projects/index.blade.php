<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-blue-900">المشاريع</h2>
                    <p class="text-gray-600 mt-1">إدارة مشاريع الإنتاج الفني</p>
                </div>
                @permission('create_project')
                <a href="{{ route('projects.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    مشروع جديد
                </a>
                @endpermission
            </div>

            @if(session('success'))
                <div class="bg-green-50 border-r-4 border-green-500 text-green-800 p-4 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($projects as $project)
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden border border-gray-100">
                        <!-- Header -->
                        <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-bold">{{ $project->name }}</h3>
                                <span class="px-3 py-1 bg-white/20 rounded-full text-xs">
                                    @if($project->type == 'series') مسلسل
                                    @elseif($project->type == 'movie') فيلم
                                    @else برنامج @endif
                                </span>
                            </div>
                            
                            <!-- Budget Progress -->
                            <div class="mt-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span>الميزانية المستخدمة</span>
                                    <span class="font-bold">{{ number_format($project->budget_percentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-white/20 rounded-full h-2">
                                    <div class="bg-white rounded-full h-2 transition-all duration-500" 
                                         style="width: {{ min($project->budget_percentage, 100) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-6">
                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">{{ number_format($project->total_budget) }}</div>
                                    <div class="text-xs text-gray-600">الميزانية الكلية</div>
                                </div>
                                <div class="text-center p-3 bg-gray-50 rounded-lg">
                                    <div class="text-2xl font-bold text-gray-700">{{ number_format($project->spent_amount) }}</div>
                                    <div class="text-xs text-gray-600">المصروف</div>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="space-y-2 text-sm text-gray-600 mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $project->start_date->format('Y/m/d') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>{{ $project->expenses_count }} مصروف</span>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mb-4">
                                @php
                                    $statusColors = [
                                        'planning' => 'bg-yellow-100 text-yellow-800',
                                        'active' => 'bg-green-100 text-green-800',
                                        'on_hold' => 'bg-orange-100 text-orange-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusNames = [
                                        'planning' => 'تخطيط',
                                        'active' => 'نشط',
                                        'on_hold' => 'متوقف',
                                        'completed' => 'مكتمل',
                                        'cancelled' => 'ملغي',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$project->status] }}">
                                    {{ $statusNames[$project->status] }}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ route('projects.show', $project) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm font-semibold transition">
                                    عرض التفاصيل
                                </a>
                                @permission('edit_project')
                                <a href="{{ route('projects.edit', $project) }}" 
                                   class="px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-semibold transition">
                                    تعديل
                                </a>
                                @endpermission
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500 text-lg">لا توجد مشاريع حالياً</p>
                        @permission('create_project')
                        <a href="{{ route('projects.create') }}" class="text-blue-600 hover:text-blue-700 font-semibold mt-2 inline-block">
                            أنشئ مشروعك الأول
                        </a>
                        @endpermission
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

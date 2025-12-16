<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                المواقع المحفوظة
            </h2>
            <div class="flex gap-2">
                @can('manage_locations')
                <a href="{{ route('locations.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    إضافة موقع جديد
                </a>
                @endcan
                <a href="{{ route('locations.map') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    عرض الخريطة
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- فلاتر البحث -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label">البحث بالاسم</label>
                        <input type="text" id="search-name" class="form-input" placeholder="اسم الموقع...">
                    </div>
                    <div>
                        <label class="form-label">المشروع</label>
                        <select id="filter-project" class="form-select">
                            <option value="">جميع المشاريع</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">الحالة</label>
                        <select id="filter-status" class="form-select">
                            <option value="">جميع الحالات</option>
                            <option value="active">نشط</option>
                            <option value="completed">مكتمل</option>
                            <option value="planned">مخطط</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button id="clear-filters" class="btn btn-outline w-full">مسح الفلاتر</button>
                    </div>
                </div>
            </div>

            <!-- جدول المواقع -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    اسم الموقع
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المشروع
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المدينة
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    العنوان
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإحداثيات
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الميزانية
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    المصروف
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    عدد المصروفات
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    تاريخ الإنشاء
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody id="locations-table" class="bg-white divide-y divide-gray-200">
                            @forelse($locations as $location)
                                <tr class="location-row hover:bg-gray-50" data-project="{{ $location->project_id }}" data-status="{{ $location->status ?? 'active' }}">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="mr-3">
                                                <div class="text-sm font-medium text-gray-900 location-name">
                                                    {{ $location->name }}
                                                </div>
                                                @if($location->map_url)
                                                    <div class="text-xs text-blue-500">
                                                        مربوط بالخريطة
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">{{ $location->project->name ?? 'غير محدد' }}</div>
                                        <div class="text-xs text-gray-500">{{ $location->project->type ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">
                                            {{ $location->city ?: 'غير محدد' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs" title="{{ $location->address }}">
                                            {{ Str::limit($location->address ?: 'غير محدد', 30) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        @if($location->latitude && $location->longitude)
                                            <div class="text-xs">
                                                <div>عرض: {{ number_format($location->latitude, 4) }}</div>
                                                <div>طول: {{ number_format($location->longitude, 4) }}</div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">غير محدد</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900">
                                        <div class="font-medium">{{ number_format($location->budget_allocated ?? 0) }}</div>
                                        <div class="text-xs text-gray-500">ر.س</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        @php
                                            $spent = $location->expenses->sum('amount') ?? 0;
                                            $budget = $location->budget_allocated ?? 0;
                                            $percentage = $budget > 0 ? ($spent / $budget) * 100 : 0;
                                        @endphp
                                        <div class="font-medium text-gray-900">{{ number_format($spent) }}</div>
                                        <div class="text-xs text-gray-500">ر.س</div>
                                        @if($budget > 0)
                                            <div class="text-xs {{ $percentage > 90 ? 'text-red-600' : ($percentage > 70 ? 'text-yellow-600' : 'text-green-600') }}">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm">
                                        <div class="font-medium text-gray-900">{{ $location->expenses->count() }}</div>
                                        <div class="text-xs text-gray-500">مصروف</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $status = $location->status ?? 'active';
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                                'planned' => 'bg-yellow-100 text-yellow-800'
                                            ];
                                            $statusTexts = [
                                                'active' => 'نشط',
                                                'completed' => 'مكتمل',
                                                'planned' => 'مخطط'
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusTexts[$status] ?? $status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        <div>{{ $location->created_at->format('Y-m-d') }}</div>
                                        <div class="text-xs">{{ $location->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium">
                                        <div class="flex gap-1 flex-wrap">
                                            <a href="{{ route('locations.show', $location) }}" class="text-blue-700 hover:text-blue-900 text-xs font-bold border border-blue-300 hover:border-blue-500 px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                                عرض
                                            </a>
                                            
                                            @if($location->latitude && $location->longitude)
                                                <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" target="_blank" class="text-green-700 hover:text-green-900 text-xs font-bold border border-green-300 hover:border-green-500 px-2 py-1 rounded bg-green-50 hover:bg-green-100">
                                                    خريطة
                                                </a>
                                            @endif
                                            
                                            @if(\App\Helpers\PermissionHelper::canEditLocation(auth()->user()))
                                                <a href="{{ route('locations.edit', $location) }}" class="text-yellow-700 hover:text-yellow-900 text-xs font-bold border border-yellow-300 hover:border-yellow-500 px-2 py-1 rounded bg-yellow-50 hover:bg-yellow-100">
                                                    تعديل
                                                </a>
                                            @endif
                                            
                                            @if(\App\Helpers\PermissionHelper::canDeleteLocation(auth()->user()))
                                                <form method="POST" action="{{ route('locations.destroy', $location) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-700 hover:text-red-900 text-xs font-bold border border-red-300 hover:border-red-500 px-2 py-1 rounded bg-red-50 hover:bg-red-100" 
                                                            onclick="return confirm('هل تريد حذف هذا الموقع؟')">
                                                        حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد مواقع</h3>
                                        <p class="mt-1 text-sm text-gray-500">ابدأ بإضافة موقع جديد</p>
                                        @can('manage_locations')
                                        <div class="mt-6">
                                            <a href="{{ route('locations.create') }}" class="btn btn-primary">
                                                إضافة موقع جديد
                                            </a>
                                        </div>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- إحصائيات سريعة -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">إجمالي المواقع</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $locations->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">المواقع النشطة</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $locations->where('status', 'active')->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">إجمالي الميزانية</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($locations->sum('budget_allocated')) }} ر.س</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mr-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">إجمالي المصروف</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ number_format($locations->sum(function($location) { return $location->expenses->sum('amount'); })) }} ر.س</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // فلترة المواقع
        function filterLocations() {
            const searchName = document.getElementById('search-name').value.toLowerCase();
            const filterProject = document.getElementById('filter-project').value;
            const filterStatus = document.getElementById('filter-status').value;
            
            const rows = document.querySelectorAll('.location-row');
            
            rows.forEach(row => {
                const name = row.querySelector('.location-name').textContent.toLowerCase();
                const project = row.dataset.project;
                const status = row.dataset.status;
                
                let show = true;
                
                if (searchName && !name.includes(searchName)) {
                    show = false;
                }
                
                if (filterProject && project !== filterProject) {
                    show = false;
                }
                
                if (filterStatus && status !== filterStatus) {
                    show = false;
                }
                
                row.style.display = show ? '' : 'none';
            });
        }
        
        // ربط الأحداث
        document.getElementById('search-name').addEventListener('input', filterLocations);
        document.getElementById('filter-project').addEventListener('change', filterLocations);
        document.getElementById('filter-status').addEventListener('change', filterLocations);
        
        document.getElementById('clear-filters').addEventListener('click', function() {
            document.getElementById('search-name').value = '';
            document.getElementById('filter-project').value = '';
            document.getElementById('filter-status').value = '';
            filterLocations();
        });
    </script>
</x-app-layout>
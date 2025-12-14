<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8 text-center">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                    <div class="flex items-center justify-center mb-3">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900 mb-2">مركز التقارير</h1>
                    <p class="text-gray-600 text-sm">تقارير شاملة وتحليلات مفصلة لجميع عمليات النظام</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-xs text-gray-600">إجمالي المشاريع</p>
                            <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Project::count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-xs text-gray-600">إجمالي العهد</p>
                            <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Custody::count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-xs text-gray-600">إجمالي المصروفات</p>
                            <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Expense::count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-xs text-gray-600">إجمالي الأشخاص</p>
                            <p class="text-lg font-semibold text-gray-900">{{ \App\Models\Person::count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reports Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- تقرير المشاريع -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-3 rounded-lg shadow-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                <div class="mr-4">
                                    <h3 class="text-base font-semibold text-gray-900">تقرير المشاريع</h3>
                                    <p class="text-sm text-gray-600">ملخص شامل لجميع المشاريع والميزانيات</p>
                                </div>
                            </div>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">{{ \App\Models\Project::count() }} مشروع</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('reports.project') }}" class="btn-primary flex-1 text-center">عرض التقرير</a>
                            @permission('export_reports')
                            <a href="{{ route('reports.project.export') }}" class="btn-secondary px-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </a>
                            @endpermission
                        </div>
                    </div>
                </div>

                <!-- تقرير الفئات -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-br from-green-500 to-green-600 p-3 rounded-lg shadow-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="mr-4">
                                    <h3 class="text-base font-semibold text-gray-900">تقرير فئات المصروفات</h3>
                                    <p class="text-sm text-gray-600">تحليل المصروفات حسب الفئات</p>
                                </div>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">{{ \App\Models\ExpenseCategory::count() }} فئة</span>
                        </div>
                        <a href="{{ route('reports.category') }}" class="btn-primary w-full text-center block">عرض التقرير التفصيلي</a>
                    </div>
                </div>

                <!-- تقرير العهد -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-3 rounded-lg shadow-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="mr-4">
                                    <h3 class="text-base font-semibold text-gray-900">تقرير العهد المالية</h3>
                                    <p class="text-sm text-gray-600">حالة ومتابعة جميع العهد</p>
                                </div>
                            </div>
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">{{ \App\Models\Custody::count() }} عهدة</span>
                        </div>
                        <a href="{{ route('reports.custody') }}" class="btn-primary w-full text-center block">عرض التقرير التفصيلي</a>
                    </div>
                </div>

                <!-- تقرير الأشخاص -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-3 rounded-lg shadow-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                </div>
                                <div class="mr-4">
                                    <h3 class="text-base font-semibold text-gray-900">تقرير الأشخاص والطاقم</h3>
                                    <p class="text-sm text-gray-600">مصروفات الممثلين والفنيين</p>
                                </div>
                            </div>
                            <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2 py-1 rounded-full">{{ \App\Models\Person::count() }} شخص</span>
                        </div>
                        <a href="{{ route('reports.person') }}" class="btn-primary w-full text-center block">عرض التقرير التفصيلي</a>
                    </div>
                </div>
                
                <!-- تقرير الاستثناءات -->
                @permission('view_exceptions')
                <div class="bg-white rounded-lg shadow-sm border border-red-200 hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-gradient-to-br from-red-500 to-red-600 p-3 rounded-lg shadow-sm">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div class="mr-4">
                                    <h3 class="text-base font-semibold text-gray-900">تقرير الاستثناءات</h3>
                                    <p class="text-sm text-gray-600">المصروفات المشبوهة والمخالفات</p>
                                </div>
                            </div>
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">مهم</span>
                        </div>
                        <a href="{{ route('reports.exceptions') }}" class="btn-danger w-full text-center block">عرض الاستثناءات</a>
                    </div>
                </div>
                @endpermission
            </div>
        </div>
    </div>
</x-app-layout>
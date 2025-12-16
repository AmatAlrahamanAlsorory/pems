<x-app-layout>
    <x-slot name="header">
        <div class="bg-white border-b border-gray-200 -mx-6 -mt-6 px-6 py-4 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">
                        لوحة التحكم - {{ Auth::user()->name }}
                    </h1>
                    <p class="text-gray-600 text-sm">نظام إدارة مصروفات الإنتاج</p>
                </div>
                <div class="text-right">
                    <div class="bg-gray-50 rounded-md px-4 py-2 border border-gray-200">
                        <div class="text-gray-900 font-medium text-sm">{{ now()->format('Y/m/d') }}</div>
                        <div class="text-gray-500 text-xs">{{ now()->format('l') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- تنبيه العهد المطلوبة للموافقة -->
            @permission('approve_custody')
                @php
                    $approvalService = app(\App\Services\ApprovalService::class);
                    $pendingCustodies = \App\Models\Custody::where('status', 'requested')
                        ->whereHas('project')
                        ->with(['project', 'requestedBy'])
                        ->get()
                        ->filter(function($custody) use ($approvalService) {
                            return $approvalService->canApprove(auth()->user(), $custody);
                        });
                @endphp
                
                @if($pendingCustodies->count() > 0)
                    <div class="mb-6 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-yellow-500 p-2 rounded-full">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">عهد تحتاج موافقة</h3>
                                    <p class="text-sm text-gray-600">
                                        يوجد {{ $pendingCustodies->count() }} عهدة تحتاج موافقتك كمحاسب إدارة
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('approvals.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                                مراجعة العهد
                            </a>
                        </div>
                    </div>
                @endif
            @endpermission
            
            <!-- إحصائيات سريعة -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
                <!-- إجمالي المشاريع -->
                <div class="stat-card border-blue-500 hover:border-blue-600 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-600 mb-1">إجمالي المشاريع</p>
                            <p class="text-xl font-semibold text-gray-900 mb-1">{{ $stats['projects_count'] }}</p>
                            <span class="bg-blue-100 text-blue-700 text-xs font-medium px-2 py-1 rounded">مشروع</span>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                @permission('manage_users')
                <!-- إجمالي المستخدمين -->
                <div class="stat-card border-purple-500 hover:border-purple-600 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-600 mb-1">إجمالي المستخدمين</p>
                            <p class="text-xl font-semibold text-gray-900 mb-1">{{ \App\Models\User::count() }}</p>
                            <span class="bg-purple-100 text-purple-700 text-xs font-medium px-2 py-1 rounded">مستخدم</span>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @endpermission

                <!-- إجمالي العهد -->
                <div class="stat-card border-green-500 hover:border-green-600 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-600 mb-1">إجمالي العهد</p>
                            <p class="text-xl font-semibold text-gray-900 mb-1">{{ $stats['active_custodies'] }}</p>
                            <span class="bg-green-100 text-green-700 text-xs font-medium px-2 py-1 rounded">عهدة</span>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- إجمالي المصروفات -->
                <div class="stat-card border-yellow-500 hover:border-yellow-600 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-600 mb-1">إجمالي المصروفات</p>
                            <p class="text-xl font-semibold text-gray-900 mb-1">{{ $stats['today_expenses'] }}</p>
                            <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-2 py-1 rounded">مصروف</span>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- المصروفات اليوم -->
                <div class="stat-card border-red-500 hover:border-red-600 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">مصروفات اليوم</p>
                            <p class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($stats['today_expenses']) }}</p>
                            <div class="text-xs text-gray-500">{{ now()->format('Y/m/d') }}</div>
                        </div>
                        <div class="bg-gradient-to-br from-red-500 to-red-600 p-4 rounded-2xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الإجراءات السريعة -->
            <div class="card mb-8 animate-slide-up">
                <div class="card-header">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        الإجراءات السريعة
                    </h3>
                </div>
                <div class="p-4 sm:p-6 md:p-8">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
                        @permission('create_project')
                        <a href="{{ route('projects.create') }}" class="group flex flex-col items-center p-4 sm:p-6 md:p-8 bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl sm:rounded-2xl transition-all duration-300 border-2 border-transparent hover:border-blue-300 hover:shadow-xl hover:-translate-y-1 sm:hover:-translate-y-2 touch-target">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-4 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">مشروع جديد</span>
                        </a>
                        @endpermission

                        @permission('create_custody')
                        <a href="{{ route('custodies.create') }}" class="group flex flex-col items-center p-8 bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-2xl transition-all duration-300 border-2 border-transparent hover:border-green-300 hover:shadow-xl hover:-translate-y-2">
                            <div class="bg-gradient-to-br from-green-500 to-green-600 p-4 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-green-700 transition-colors">عهدة جديدة</span>
                        </a>
                        @endpermission

                        @permission('create_expense')
                        <a href="{{ route('expenses.create') }}" class="group flex flex-col items-center p-8 bg-gradient-to-br from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 rounded-2xl transition-all duration-300 border-2 border-transparent hover:border-yellow-300 hover:shadow-xl hover:-translate-y-2">
                            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-4 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-yellow-700 transition-colors">تسجيل مصروف</span>
                        </a>
                        @endpermission

                        @permission('view_reports')
                        <a href="{{ route('reports.index') }}" class="group flex flex-col items-center p-8 bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-2xl transition-all duration-300 border-2 border-transparent hover:border-purple-300 hover:shadow-xl hover:-translate-y-2">
                            <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-4 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">التقارير</span>
                        </a>
                        @endpermission

                        @permission('manage_users')
                        <a href="{{ route('users.index') }}" class="group flex flex-col items-center p-8 bg-gradient-to-br from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-2xl transition-all duration-300 border-2 border-transparent hover:border-indigo-300 hover:shadow-xl hover:-translate-y-2">
                            <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 p-4 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">إدارة المستخدمين</span>
                        </a>
                        @endpermission

                        @permission('view_reports')
                        <a href="{{ route('analytics.index') }}" class="group flex flex-col items-center p-8 bg-gradient-to-br from-pink-50 to-pink-100 hover:from-pink-100 hover:to-pink-200 rounded-2xl transition-all duration-300 border-2 border-transparent hover:border-pink-300 hover:shadow-xl hover:-translate-y-2">
                            <div class="bg-gradient-to-br from-pink-500 to-pink-600 p-4 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-pink-700 transition-colors">التحليلات التنبؤية</span>
                        </a>
                        @endpermission
                        

                        @permission('manage_locations')
                        <a href="{{ route('locations.map') }}" class="group flex flex-col items-center p-8 bg-gradient-to-br from-teal-50 to-teal-100 hover:from-teal-100 hover:to-teal-200 rounded-2xl transition-all duration-300 border-2 border-transparent hover:border-teal-300 hover:shadow-xl hover:-translate-y-2">
                            <div class="bg-gradient-to-br from-teal-500 to-teal-600 p-4 rounded-2xl mb-4 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-bold text-gray-900 group-hover:text-teal-700 transition-colors">خريطة المواقع</span>
                        </a>
                        @endpermission
                    </div>
                </div>
            </div>

            <!-- الرسوم البيانية -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 mb-6 sm:mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 md:p-6 chart-container">
                    <canvas id="budgetChart" width="400" height="300"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 md:p-6 chart-container">
                    <canvas id="categoryChart" width="400" height="300"></canvas>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 md:p-6 chart-container md:col-span-2 lg:col-span-1">
                    <canvas id="monthlyChart" width="400" height="300"></canvas>
                </div>
            </div>
            
            <!-- المشاريع الحرجة والتنبيهات والموافقات -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                <!-- المشاريع الحرجة -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="p-4 sm:p-6 border-b border-gray-200 bg-gradient-to-r from-orange-600 to-orange-700">
                        <h3 class="text-base sm:text-lg font-bold text-white">المشاريع الحرجة</h3>
                    </div>
                    <div class="p-4 sm:p-6 max-h-60 sm:max-h-80 overflow-y-auto">
                        @if($criticalProjects->count() > 0)
                            <div class="space-y-3">
                                @foreach($criticalProjects as $project)
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border-r-4 border-red-500">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $project->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ number_format($project->budget_percentage, 1) }}% من الميزانية</p>
                                        </div>
                                        <a href="{{ route('projects.show', $project) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            عرض
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">جميع المشاريع في الحدود الآمنة</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- التنبيهات -->
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="p-4 sm:p-6 border-b border-gray-200 bg-gradient-to-r from-red-600 to-red-700">
                        <h3 class="text-base sm:text-lg font-bold text-white">التنبيهات والإشعارات</h3>
                    </div>
                    <div class="p-4 sm:p-6 max-h-60 sm:max-h-80 overflow-y-auto">
                        @if($notifications->count() > 0)
                            <div class="space-y-3">
                                @foreach($notifications as $notification)
                                    <div class="flex items-start p-3 rounded-lg border-r-4 
                                        @if($notification->level == 'critical') border-red-600 bg-red-50
                                        @elseif($notification->level == 'danger') border-orange-500 bg-orange-50
                                        @elseif($notification->level == 'warning') border-yellow-500 bg-yellow-50
                                        @else border-blue-500 bg-blue-50 @endif">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $notification->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                            <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">لا توجد تنبيهات جديدة</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- الموافقات المطلوبة -->
                @if(\App\Helpers\PermissionHelper::canViewReports(auth()->user()))
                <div class="bg-white overflow-hidden shadow-lg rounded-xl md:col-span-2 lg:col-span-1">
                    <div class="p-4 sm:p-6 border-b border-gray-200 bg-gradient-to-r from-yellow-600 to-yellow-700">
                        <h3 class="text-base sm:text-lg font-bold text-white">الموافقات المطلوبة</h3>
                    </div>
                    <div class="p-4 sm:p-6 max-h-60 sm:max-h-80 overflow-y-auto">
                        @if($pendingApprovals->count() > 0)
                            <div class="space-y-3">
                                @foreach($pendingApprovals as $approval)
                                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border-r-4 border-yellow-500">
                                        <div>
                                            <h4 class="font-medium text-gray-900">
                                                @if($approval->approvable_type === 'App\\Models\\Expense')
                                                    مصروف - {{ $approval->approvable?->expenseCategory?->name ?? 'غير محدد' }}
                                                @else
                                                    عهدة - {{ $approval->approvable?->project?->name ?? 'غير محدد' }}
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-600">{{ number_format($approval->approvable?->amount ?? 0) }} ر.س</p>
                                        </div>
                                        <a href="{{ route('approvals.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            مراجعة
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm">لا توجد موافقات مطلوبة</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = @json($chartData);
            
            // رسم بياني لحالة المشاريع
            const budgetCtx = document.getElementById('budgetChart');
            if (budgetCtx && chartData.project_status) {
                const normal = parseInt(chartData.project_status.normal) || 0;
                const warning = parseInt(chartData.project_status.warning) || 0;
                const critical = parseInt(chartData.project_status.critical) || 0;
                const total = normal + warning + critical;
                
                if (total > 0) {
                    new Chart(budgetCtx, {
                        type: 'doughnut',
                        data: {
                            labels: [`آمن (${normal})`, `تحذير (${warning})`, `خطر (${critical})`],
                            datasets: [{
                                data: [normal, warning, critical],
                                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                borderWidth: 3,
                                borderColor: '#ffffff',
                                hoverBorderWidth: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { 
                                        padding: 15, 
                                        usePointStyle: true,
                                        font: { size: 12 }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'حالة ميزانيات المشاريع',
                                    font: { size: 14, weight: 'bold' }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                                            return context.label + ': ' + percentage + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    budgetCtx.getContext('2d').font = '16px Arial';
                    budgetCtx.getContext('2d').textAlign = 'center';
                    budgetCtx.getContext('2d').fillText('لا توجد مشاريع', budgetCtx.width/2, budgetCtx.height/2);
                }
            }
            
            // رسم بياني للمصروفات حسب الفئات
            const categoryCtx = document.getElementById('categoryChart');
            if (categoryCtx && chartData.category_spending && chartData.category_spending.length > 0) {
                const sortedData = chartData.category_spending.slice(0, 5);
                new Chart(categoryCtx, {
                    type: 'bar',
                    data: {
                        labels: sortedData.map(item => item.name || 'غير محدد'),
                        datasets: [{
                            label: 'المبلغ المصروف (ر.س)',
                            data: sortedData.map(item => parseFloat(item.amount) || 0),
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
                            ],
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'المصروفات حسب الفئات',
                                font: { size: 14, weight: 'bold' }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('ar-SA').format(value) + ' ر.س';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                // عرض رسالة عند عدم وجود بيانات
                categoryCtx.getContext('2d').font = '16px Arial';
                categoryCtx.getContext('2d').textAlign = 'center';
                categoryCtx.getContext('2d').fillText('لا توجد بيانات للعرض', categoryCtx.width/2, categoryCtx.height/2);
            }
            
            // رسم بياني للمصروفات الشهرية
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx && chartData.monthly_expenses) {
                const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                               'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                
                // تحويل بيانات الشهور إلى مصفوفة
                const monthlyData = [];
                for (let i = 1; i <= 12; i++) {
                    const monthKey = i.toString().padStart(2, '0');
                    monthlyData.push(parseFloat(chartData.monthly_expenses[monthKey]) || 0);
                }
                
                const hasData = monthlyData.some(value => value > 0);
                
                if (hasData) {
                    new Chart(monthlyCtx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'المصروفات الشهرية (ر.س)',
                                data: monthlyData,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'اتجاه المصروفات الشهرية',
                                    font: { size: 14, weight: 'bold' }
                                },
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return new Intl.NumberFormat('ar-SA').format(value) + ' ر.س';
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    monthlyCtx.getContext('2d').font = '16px Arial';
                    monthlyCtx.getContext('2d').textAlign = 'center';
                    monthlyCtx.getContext('2d').fillText('لا توجد بيانات شهرية', monthlyCtx.width/2, monthlyCtx.height/2);
                }
            }
        });
    </script>
    @vite(['resources/js/notifications.js'])
</x-app-layout>

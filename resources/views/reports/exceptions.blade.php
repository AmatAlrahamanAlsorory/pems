<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">تقرير الاستثناءات والمخالفات</h2>
                <p class="text-gray-600">المصروفات المشبوهة والمخالفات المكتشفة</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="card p-4 bg-red-50 border-red-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-red-600">عالية الخطورة</p>
                            <p class="text-2xl font-bold text-red-900">{{ $exceptions->where('severity', 'high')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="card p-4 bg-yellow-50 border-yellow-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-yellow-600">متوسطة الخطورة</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ $exceptions->where('severity', 'medium')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="card p-4 bg-blue-50 border-blue-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-blue-600">منخفضة الخطورة</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $exceptions->where('severity', 'low')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="card p-4 bg-gray-50 border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-gray-100 rounded-lg">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm font-medium text-gray-600">إجمالي الاستثناءات</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $exceptions->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="p-6">
                    @if($exceptions->count() > 0)
                        <div class="space-y-4">
                            @foreach($exceptions as $exception)
                                <div class="border rounded-lg p-4 
                                    {{ $exception['severity'] === 'high' ? 'border-red-200 bg-red-50' : 
                                       ($exception['severity'] === 'medium' ? 'border-yellow-200 bg-yellow-50' : 'border-blue-200 bg-blue-50') }}">
                                    
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $exception['severity'] === 'high' ? 'bg-red-100 text-red-800' : 
                                                       ($exception['severity'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                                    {{ $exception['title'] }}
                                                </span>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                <div>
                                                    <p class="font-medium text-gray-900">رقم المصروف</p>
                                                    <p class="text-gray-600">{{ $exception['expense']->expense_number }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">المشروع</p>
                                                    <p class="text-gray-600">{{ $exception['expense']->project->name }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">المبلغ</p>
                                                    <p class="text-gray-600">{{ number_format($exception['expense']->amount) }} {{ $exception['expense']->currency }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">الفئة</p>
                                                    <p class="text-gray-600">{{ $exception['expense']->category->name }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">تاريخ المصروف</p>
                                                    <p class="text-gray-600">{{ $exception['expense']->expense_date->format('Y-m-d') }}</p>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">المستخدم</p>
                                                    <p class="text-gray-600">{{ $exception['expense']->user->name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mr-4">
                                            <a href="{{ route('expenses.show', $exception['expense']) }}" 
                                               class="btn btn-sm btn-outline">
                                                عرض التفاصيل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">لا توجد استثناءات</h3>
                            <p class="mt-1 text-sm text-gray-500">جميع المصروفات تبدو طبيعية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
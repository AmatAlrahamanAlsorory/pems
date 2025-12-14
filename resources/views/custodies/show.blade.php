<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-blue-900">تفاصيل العهدة</h2>
                    <p class="text-gray-600 mt-1">{{ $custody->project->name }}</p>
                </div>
                <a href="{{ route('custodies.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold transition">
                    رجوع
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Custody Details -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                            <h3 class="text-xl font-bold">معلومات العهدة</h3>
                        </div>
                        <div class="p-6 grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-sm text-gray-600">المشروع</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $custody->project->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">المسؤول</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $custody->requestedBy->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">الغرض</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $custody->purpose }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">تاريخ الإنشاء</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $custody->created_at->format('Y/m/d') }}</p>
                            </div>
                        </div>
                        @if($custody->notes)
                        <div class="px-6 pb-6">
                            <p class="text-sm text-gray-600 mb-2">ملاحظات</p>
                            <p class="text-gray-700">{{ $custody->notes }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Expenses List -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                            <h3 class="text-xl font-bold">المصروفات من هذه العهدة</h3>
                        </div>
                        <div class="p-6">
                            @forelse($custody->expenses as $expense)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $expense->category->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $expense->description }}</p>
                                        <p class="text-xs text-gray-500">{{ $expense->expense_date->format('Y/m/d') }}</p>
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
                    <!-- Status Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-green-600 to-green-700 p-6 text-white">
                            <h3 class="text-xl font-bold">الحالة المالية</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">مبلغ العهدة</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($custody->amount) }} ر.س</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">المصروف</p>
                                <p class="text-2xl font-bold text-red-600">{{ number_format($custody->spent_amount) }} ر.س</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">المتبقي</p>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($custody->amount - $custody->spent_amount) }} ر.س</p>
                            </div>
                            <div class="pt-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">نسبة الصرف</span>
                                    <span class="font-bold text-gray-900">{{ number_format(($custody->spent_amount / $custody->amount) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full h-3 transition-all" 
                                         style="width: {{ min(($custody->spent_amount / $custody->amount) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-l from-purple-600 to-purple-700 p-6 text-white">
                            <h3 class="text-xl font-bold">الإجراءات</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if($custody->status == 'pending')
                                <form action="{{ route('custodies.approve', $custody) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition">
                                        الموافقة على العهدة
                                    </button>
                                </form>
                            @endif
                            
                            @if($custody->status == 'approved')
                                <form action="{{ route('custodies.settle', $custody) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                                        تصفية العهدة
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('expenses.create') }}?custody_id={{ $custody->id }}" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-center py-3 rounded-lg font-semibold transition">
                                إضافة مصروف
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

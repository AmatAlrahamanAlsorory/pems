<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-blue-900">تفاصيل المصروف</h2>
                    <p class="text-gray-600 mt-1">{{ $expense->project->name }}</p>
                </div>
                <div class="flex gap-3">
                    @if($expense->status == 'pending')
                        <form action="{{ route('expenses.approve', $expense) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow-lg transition">
                                موافقة
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('expenses.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold transition">
                        رجوع
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-l from-blue-600 to-blue-700 p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $expense->category->name_ar ?? $expense->category->name ?? 'غير محدد' }}</h3>
                            <p class="text-blue-100 mt-1">{{ $expense->item->name_ar ?? $expense->item->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="text-left">
                            <p class="text-3xl font-bold">{{ number_format($expense->amount) }}</p>
                            <p class="text-blue-100">ريال سعودي</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">المشروع</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $expense->project->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">تاريخ المصروف</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('Y/m/d') : 'غير محدد' }}</p>
                        </div>
                        @if($expense->custody)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">العهدة</p>
                            <p class="text-lg font-semibold text-gray-900">عهدة رقم #{{ $expense->custody->id }}</p>
                        </div>
                        @endif
                        @if($expense->invoice_number)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">رقم الفاتورة</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $expense->invoice_number }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600 mb-1">المسجل بواسطة</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $expense->user->name ?? 'غير محدد' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">الحالة</p>
                            @php
                                $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                                $statusNames = ['pending' => 'قيد المراجعة', 'approved' => 'موافق عليه', 'rejected' => 'مرفوض'];
                            @endphp
                            <span class="px-4 py-2 rounded-full text-sm font-bold {{ $statusColors[$expense->status] }}">
                                {{ $statusNames[$expense->status] }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">الوصف</p>
                        <p class="text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $expense->description }}</p>
                    </div>

                    @if($expense->notes)
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">ملاحظات</p>
                        <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">{{ $expense->notes }}</p>
                    </div>
                    @endif

                    @if($expense->invoice_file)
                    <div>
                        <p class="text-sm text-gray-600 mb-2">الفاتورة</p>
                        <img src="{{ Storage::url($expense->invoice_file) }}" alt="Invoice" class="max-w-full rounded-lg shadow-md">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

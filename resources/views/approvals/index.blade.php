<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            الموافقات المطلوبة
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if($pendingApprovals->count() > 0)
                <div class="space-y-6">
                    @foreach($pendingApprovals as $approval)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-r-4 border-yellow-500">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            @if($approval->approvable_type === 'App\\Models\\Expense')
                                                مصروف - {{ $approval->approvable->category->name }}
                                            @elseif($approval->approvable_type === 'App\\Models\\Custody')
                                                عهدة - {{ $approval->approvable->project->name }}
                                            @endif
                                        </h3>
                                        <p class="text-gray-600">طلب من: {{ $approval->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $approval->created_at->diffForHumans() }}</p>
                                        @if($approval->approvable_type === 'App\\Models\\Custody')
                                            <span class="inline-block mt-2 px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">
                                                يتطلب موافقة محاسب الإدارة
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <p class="text-2xl font-bold text-blue-600">
                                            {{ number_format($approval->approvable->amount) }} 
                                            @if($approval->approvable_type === 'App\\Models\\Custody')
                                                {{ $approval->approvable->currency ?? 'ر.س' }}
                                            @else
                                                ر.س
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if($approval->approvable_type === 'App\\Models\\Expense')
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 text-sm">
                                        <div>
                                            <span class="text-gray-600">المشروع:</span>
                                            <span class="font-medium">{{ $approval->approvable->project->name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">البند:</span>
                                            <span class="font-medium">{{ $approval->approvable->item->name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">التاريخ:</span>
                                            <span class="font-medium">{{ $approval->approvable->expense_date->format('Y/m/d') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">رقم الفاتورة:</span>
                                            <span class="font-medium">{{ $approval->approvable->invoice_number ?? 'غير محدد' }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($approval->approvable->description)
                                        <div class="mb-4">
                                            <span class="text-gray-600 text-sm">الوصف:</span>
                                            <p class="text-gray-900">{{ $approval->approvable->description }}</p>
                                        </div>
                                    @endif
                                @endif

                                @if($approval->approvable_type === 'App\\Models\\Custody')
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4 text-sm">
                                        <div>
                                            <span class="text-gray-600">الغرض:</span>
                                            <span class="font-medium">{{ $approval->approvable->purpose }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">المشروع:</span>
                                            <span class="font-medium">{{ $approval->approvable->project->name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-600">رقم العهدة:</span>
                                            <span class="font-medium">{{ $approval->approvable->custody_number ?? 'C-' . $approval->approvable->id }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($approval->approvable->notes)
                                        <div class="mb-4">
                                            <span class="text-gray-600 text-sm">ملاحظات:</span>
                                            <p class="text-gray-900">{{ $approval->approvable->notes }}</p>
                                        </div>
                                    @endif
                                @endif

                                <!-- أزرار الموافقة والرفض -->
                                <div class="flex gap-3 pt-4 border-t">
                                    @php
                                        $approvalId = $approval->id ?? $approval->id;
                                        if($approval->approvable_type === 'App\\Models\\Custody') {
                                            $approvalId = 'custody_' . $approval->approvable->id;
                                        }
                                    @endphp
                                    
                                    <form method="POST" action="{{ route('approvals.approve', $approvalId) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition"
                                                onclick="return confirm('هل أنت متأكد من الموافقة؟')">
                                            ✓ موافقة
                                        </button>
                                    </form>
                                    
                                    <button type="button" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition"
                                            onclick="showRejectModal('{{ $approvalId }}')">
                                        ✗ رفض
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- نموذج الرفض -->
                        @php
                            $modalId = $approval->id ?? $approval->id;
                            if($approval->approvable_type === 'App\\Models\\Custody') {
                                $modalId = 'custody_' . $approval->approvable->id;
                            }
                        @endphp
                        
                        <div id="rejectModal{{ $modalId }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                                <h3 class="text-lg font-bold mb-4">رفض الطلب</h3>
                                <form method="POST" action="{{ route('approvals.reject', $modalId) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">سبب الرفض</label>
                                        <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                                                  placeholder="اختياري - اكتب سبب الرفض"></textarea>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                            تأكيد الرفض
                                        </button>
                                        <button type="button" onclick="hideRejectModal('{{ $modalId }}')" 
                                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                                            إلغاء
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد موافقات مطلوبة</h3>
                    <p class="text-gray-500">جميع الطلبات تم معالجتها</p>
                </div>
            @endif

        </div>
    </div>

    <script>
        function showRejectModal(id) {
            document.getElementById('rejectModal' + id).classList.remove('hidden');
        }
        
        function hideRejectModal(id) {
            document.getElementById('rejectModal' + id).classList.add('hidden');
        }
    </script>
</x-app-layout>
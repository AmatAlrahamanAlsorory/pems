<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            @endif
            
            @php
                $pendingCustodies = $custodies->where('status', 'requested');
            @endphp
            
            @if($pendingCustodies->count() > 0 && in_array(auth()->user()->role, ['financial_manager', 'admin_accountant', 'production_manager']))
                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="bg-yellow-500 p-2 rounded-full">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">عهد تحتاج موافقة</h3>
                            <p class="text-sm text-gray-600">يوجد {{ $pendingCustodies->count() }} عهدة تحتاج موافقتك</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">العهد المالية</h2>
                @permission('create_custody')
                <a href="{{ route('custodies.create') }}" class="btn-primary">عهدة جديدة</a>
                @endpermission
            </div>

            <div class="card">
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>رقم العهدة</th>
                                <th>المشروع</th>
                                <th>المبلغ</th>
                                <th>الغرض</th>
                                <th>الحالة</th>
                                <th>تاريخ الطلب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($custodies as $custody)
                                <tr class="{{ $custody->status == 'requested' ? 'bg-yellow-50 border-l-4 border-yellow-400' : '' }}">
                                    <td class="font-medium">{{ $custody->custody_number ?? 'C-' . $custody->id }}</td>
                                    <td>{{ $custody->project->name }}</td>
                                    <td>{{ number_format($custody->amount) }} ر.س</td>
                                    <td>{{ $custody->purpose }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($custody->status == 'approved') badge-success
                                            @elseif($custody->status == 'requested') badge-warning
                                            @elseif($custody->status == 'active') badge-info
                                            @else badge-secondary
                                            @endif">
                                            @if($custody->status == 'requested') يحتاج موافقة
                                            @elseif($custody->status == 'approved') موافق عليه
                                            @elseif($custody->status == 'active') نشط
                                            @else {{ $custody->status }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $custody->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="{{ route('custodies.show', $custody) }}" class="text-blue-600 hover:text-blue-800 text-sm">عرض</a>
                                            @if($custody->status == 'requested' && in_array(auth()->user()->role, ['financial_manager', 'admin_accountant', 'production_manager']))
                                                <form method="POST" action="{{ route('approvals.approve', 'custody_' . $custody->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium" 
                                                            onclick="return confirm('هل تريد الموافقة على هذه العهدة؟')">
                                                        ✓ موافقة
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">لا توجد عهد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($custodies->hasPages())
                    <div class="p-4">
                        {{ $custodies->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
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
            
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">المصروفات</h2>
                @permission('create_expense')
                <a href="{{ route('expenses.create') }}" class="btn-primary">مصروف جديد</a>
                @endpermission
            </div>

            <div class="card">
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>المشروع</th>
                                <th>الفئة</th>
                                <th>الوصف</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date }}</td>
                                    <td>{{ $expense->project->name }}</td>
                                    <td>{{ $expense->category->name_ar }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td>{{ number_format($expense->amount) }} {{ $expense->currency ?? 'YER' }}</td>
                                    <td>
                                        @php
                                            $statusNames = [
                                                'pending' => 'قيد المراجعة',
                                                'approved' => 'موافق عليه',
                                                'rejected' => 'مرفوض',
                                                'confirmed' => 'مؤكد'
                                            ];
                                            $statusColors = [
                                                'pending' => 'badge-warning',
                                                'approved' => 'badge-success',
                                                'rejected' => 'badge-danger',
                                                'confirmed' => 'badge-info'
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusColors[$expense->status] ?? 'badge-secondary' }}">
                                            {{ $statusNames[$expense->status] ?? $expense->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-1">
                                            <a href="{{ route('expenses.show', $expense) }}" class="text-blue-700 hover:text-blue-900 text-xs font-bold border border-blue-300 hover:border-blue-500 px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                                عرض
                                            </a>
                                            
                                            @if(\App\Helpers\PermissionHelper::canEditExpense(auth()->user()))
                                                <a href="{{ route('expenses.edit', $expense) }}" class="text-yellow-700 hover:text-yellow-900 text-xs font-bold border border-yellow-300 hover:border-yellow-500 px-2 py-1 rounded bg-yellow-50 hover:bg-yellow-100">
                                                    تعديل
                                                </a>
                                            @endif
                                            
                                            @if(\App\Helpers\PermissionHelper::canDeleteExpense(auth()->user()))
                                                <form method="POST" action="{{ route('expenses.destroy', $expense) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-700 hover:text-red-900 text-xs font-bold border border-red-300 hover:border-red-500 px-2 py-1 rounded bg-red-50 hover:bg-red-100" 
                                                            onclick="return confirm('هل تريد حذف هذا المصروف؟')">
                                                        حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">لا توجد مصروفات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($expenses->hasPages())
                    <div class="p-4">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
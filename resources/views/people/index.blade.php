<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">الأشخاص</h2>
                <a href="{{ route('people.create') }}" class="btn-primary">إضافة شخص</a>
            </div>

            <div class="card">
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>الهاتف</th>
                                <th>رقم الهوية</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($people as $person)
                                <tr>
                                    <td class="font-medium">{{ $person->name }}</td>
                                    <td>{{ $person->type_name }}</td>
                                    <td>{{ $person->phone ?? '-' }}</td>
                                    <td>{{ $person->id_number ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $person->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $person->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex gap-1">
                                            <a href="{{ route('people.show', $person) }}" class="text-blue-700 hover:text-blue-900 text-xs font-bold border border-blue-300 hover:border-blue-500 px-2 py-1 rounded bg-blue-50 hover:bg-blue-100">
                                                عرض
                                            </a>
                                            
                                            @if(\App\Helpers\PermissionHelper::canEditPerson(auth()->user()))
                                                <a href="{{ route('people.edit', $person) }}" class="text-yellow-700 hover:text-yellow-900 text-xs font-bold border border-yellow-300 hover:border-yellow-500 px-2 py-1 rounded bg-yellow-50 hover:bg-yellow-100">
                                                    تعديل
                                                </a>
                                            @endif
                                            
                                            @if(\App\Helpers\PermissionHelper::canDeletePerson(auth()->user()))
                                                <form method="POST" action="{{ route('people.destroy', $person) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-700 hover:text-red-900 text-xs font-bold border border-red-300 hover:border-red-500 px-2 py-1 rounded bg-red-50 hover:bg-red-100" 
                                                            onclick="return confirm('هل تريد حذف هذا الشخص؟')">
                                                        حذف
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">لا توجد أشخاص</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($people->hasPages())
                    <div class="p-4">
                        {{ $people->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
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
                                        <a href="{{ route('people.show', $person) }}" class="text-blue-600 hover:text-blue-800 text-sm">عرض</a>
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
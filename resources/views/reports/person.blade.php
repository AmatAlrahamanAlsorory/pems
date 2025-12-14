<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">تقرير الأشخاص</h2>
                    <p class="text-gray-600 text-sm">مصروفات الأشخاص والطاقم</p>
                </div>
                <div class="flex gap-2">
                    @permission('export_reports')
                    <a href="{{ route('reports.person', ['format' => 'excel']) }}" class="btn-primary">تصدير Excel</a>
                    <a href="{{ route('reports.person', ['format' => 'pdf']) }}" class="btn-primary">تصدير PDF</a>
                    @endpermission
                    <a href="{{ route('reports.index') }}" class="btn-secondary">رجوع</a>
                </div>
            </div>

            <div class="card">
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>عدد المصروفات</th>
                                <th>إجمالي المبلغ</th>
                                <th>متوسط المصروف</th>
                                <th>الهاتف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($people as $person)
                                @php
                                    $totalExpenses = $person->expenses->sum('amount');
                                    $expensesCount = $person->expenses->count();
                                @endphp
                                <tr>
                                    <td class="font-medium">{{ $person->name }}</td>
                                    <td>{{ $person->type_name }}</td>
                                    <td>{{ $expensesCount }}</td>
                                    <td>{{ number_format($totalExpenses) }} ر.س</td>
                                    <td>{{ $expensesCount > 0 ? number_format($totalExpenses / $expensesCount) : 0 }} ر.س</td>
                                    <td>{{ $person->phone ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
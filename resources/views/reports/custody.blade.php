<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">تقرير العهد</h2>
                    <p class="text-gray-600 text-sm">حالة العهد المالية</p>
                </div>
                <div class="flex gap-2">
                    @permission('export_reports')
                    <a href="{{ route('reports.custody', ['format' => 'excel']) }}" class="btn-primary">تصدير Excel</a>
                    <a href="{{ route('reports.custody', ['format' => 'pdf']) }}" class="btn-primary">تصدير PDF</a>
                    @endpermission
                    <a href="{{ route('reports.index') }}" class="btn-secondary">رجوع</a>
                </div>
            </div>

            <div class="card">
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>رقم العهدة</th>
                                <th>المشروع</th>
                                <th>المستلم</th>
                                <th>المبلغ</th>
                                <th>المصروف</th>
                                <th>المتبقي</th>
                                <th>الحالة</th>
                                <th>تاريخ الطلب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($custodies as $custody)
                                <tr>
                                    <td class="font-medium">{{ $custody->custody_number }}</td>
                                    <td>{{ $custody->project->name }}</td>
                                    <td>{{ $custody->requestedBy->name }}</td>
                                    <td>{{ number_format($custody->amount) }} ر.س</td>
                                    <td>{{ number_format($custody->spent_amount) }} ر.س</td>
                                    <td>{{ number_format($custody->remaining_amount) }} ر.س</td>
                                    <td>
                                        <span class="badge {{ $custody->status == 'approved' ? 'badge-success' : 'badge-warning' }}">
                                            {{ $custody->status }}
                                        </span>
                                    </td>
                                    <td>{{ $custody->request_date }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-500">لا توجد عهد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
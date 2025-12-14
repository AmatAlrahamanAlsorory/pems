<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">تقرير الفئات</h2>
                    <p class="text-gray-600 text-sm">المصروفات حسب فئات المصروفات</p>
                </div>
                <div class="flex gap-2">
                    @permission('export_reports')
                    <a href="{{ route('reports.category', ['format' => 'excel']) }}" class="btn-primary">تصدير Excel</a>
                    <a href="{{ route('reports.category', ['format' => 'pdf']) }}" class="btn-primary">تصدير PDF</a>
                    @endpermission
                    <a href="{{ route('reports.index') }}" class="btn-secondary">رجوع</a>
                </div>
            </div>

            <div class="card">
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>اسم الفئة</th>
                                <th>عدد المصروفات</th>
                                <th>إجمالي المبلغ</th>
                                <th>متوسط المبلغ</th>
                                <th>النسبة من الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                @php
                                    $categoryTotal = $category->expenses->sum('amount');
                                    $percentage = $totalSpent > 0 ? ($categoryTotal / $totalSpent) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="font-medium">{{ $category->name_ar }}</td>
                                    <td>{{ $category->expenses->count() }}</td>
                                    <td>{{ number_format($categoryTotal) }} ر.س</td>
                                    <td>{{ $category->expenses->count() > 0 ? number_format($categoryTotal / $category->expenses->count()) : 0 }} ر.س</td>
                                    <td>
                                        <span class="badge badge-info">{{ number_format($percentage, 1) }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-gray-500">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th class="font-bold">الإجمالي</th>
                                <th>{{ $categories->sum(fn($cat) => $cat->expenses->count()) }}</th>
                                <th>{{ number_format($totalSpent) }} ر.س</th>
                                <th>-</th>
                                <th>100%</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
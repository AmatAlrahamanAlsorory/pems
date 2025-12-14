<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">تقرير المشاريع</h2>
                    <p class="text-gray-600 text-sm">ملخص شامل لجميع المشاريع</p>
                </div>
                <div class="flex gap-2">
                    @permission('export_reports')
                    <a href="{{ route('reports.project') }}?format=excel" class="btn-primary">تصدير Excel</a>
                    <a href="{{ route('reports.project.print') }}" target="_blank" class="btn-primary">🖨️ طباعة PDF</a>
                    @endpermission
                    <a href="{{ route('reports.index') }}" class="btn-secondary">رجوع</a>
                </div>
            </div>

            <div class="card">
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>اسم المشروع</th>
                                <th>النوع</th>
                                <th>الميزانية الكلية</th>
                                <th>المصروف</th>
                                <th>المتبقي</th>
                                <th>النسبة</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr>
                                    <td class="font-medium">{{ $project->name }}</td>
                                    <td>
                                        @if($project->type == 'series') مسلسل
                                        @elseif($project->type == 'movie') فيلم
                                        @else برنامج @endif
                                    </td>
                                    <td>{{ number_format($project->total_budget) }} ر.س</td>
                                    <td>{{ number_format($project->spent_amount) }} ر.س</td>
                                    <td>{{ number_format($project->total_budget - $project->spent_amount) }} ر.س</td>
                                    <td>
                                        @php
                                            $percentage = $project->total_budget > 0 ? ($project->spent_amount / $project->total_budget) * 100 : 0;
                                        @endphp
                                        <span class="badge {{ $percentage >= 90 ? 'badge-danger' : ($percentage >= 70 ? 'badge-warning' : 'badge-success') }}">
                                            {{ number_format($percentage, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $project->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-gray-500">لا توجد مشاريع</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
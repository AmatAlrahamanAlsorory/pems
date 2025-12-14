<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>تقرير المشروع - {{ $project->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table th, .info-table td { border: 1px solid #ddd; padding: 8px; text-align: right; }
        .info-table th { background-color: #f5f5f5; }
        .section { margin-bottom: 30px; }
        .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .status-active { color: #10b981; }
        .status-completed { color: #6b7280; }
        .status-blocked { color: #ef4444; }
        .budget-normal { color: #10b981; }
        .budget-warning { color: #f59e0b; }
        .budget-danger { color: #ef4444; }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير المشروع التفصيلي</h1>
        <h2>{{ $project->name }}</h2>
        <p>تاريخ التقرير: {{ $generated_at }}</p>
    </div>

    <div class="section">
        <h3>معلومات المشروع</h3>
        <table class="info-table">
            <tr>
                <th>اسم المشروع</th>
                <td>{{ $project->name }}</td>
                <th>نوع المشروع</th>
                <td>{{ $project->type }}</td>
            </tr>
            <tr>
                <th>الحالة</th>
                <td class="status-{{ $project->status }}">
                    @switch($project->status)
                        @case('active') نشط @break
                        @case('completed') مكتمل @break
                        @case('blocked') محظور @break
                        @default {{ $project->status }}
                    @endswitch
                </td>
                <th>عدد الحلقات</th>
                <td>{{ $project->episodes_count ?? 'غير محدد' }}</td>
            </tr>
            <tr>
                <th>تاريخ البداية</th>
                <td>{{ $project->start_date?->format('Y-m-d') ?? 'غير محدد' }}</td>
                <th>تاريخ النهاية</th>
                <td>{{ $project->end_date?->format('Y-m-d') ?? 'غير محدد' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>الميزانية والمصروفات</h3>
        <table class="info-table">
            <tr>
                <th>إجمالي الميزانية</th>
                <td>{{ number_format($project->total_budget, 2) }} ر.س</td>
                <th>المبلغ المصروف</th>
                <td>{{ number_format($project->spent_amount, 2) }} ر.س</td>
            </tr>
            <tr>
                <th>المبلغ المتبقي</th>
                <td>{{ number_format($project->remaining_budget, 2) }} ر.س</td>
                <th>نسبة الصرف</th>
                <td class="budget-{{ $project->budget_status }}">
                    {{ number_format($budget_percentage, 1) }}%
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>المصروفات حسب الفئات</h3>
        <table class="info-table">
            <thead>
                <tr>
                    <th>الفئة</th>
                    <th>عدد المصروفات</th>
                    <th>إجمالي المبلغ</th>
                    <th>متوسط المصروف</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses_by_category as $categoryName => $expenses)
                <tr>
                    <td>{{ $categoryName }}</td>
                    <td>{{ $expenses->count() }}</td>
                    <td>{{ number_format($expenses->sum('amount'), 2) }} ر.س</td>
                    <td>{{ number_format($expenses->avg('amount'), 2) }} ر.س</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>ملخص العهد</h3>
        <table class="info-table">
            <thead>
                <tr>
                    <th>رقم العهدة</th>
                    <th>المبلغ</th>
                    <th>المصروف</th>
                    <th>المتبقي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->custodies as $custody)
                <tr>
                    <td>{{ $custody->custody_number ?? 'C-' . $custody->id }}</td>
                    <td>{{ number_format($custody->amount, 2) }} ر.س</td>
                    <td>{{ number_format($custody->returned_amount, 2) }} ر.س</td>
                    <td>{{ number_format($custody->remaining_amount, 2) }} ر.س</td>
                    <td>{{ $custody->status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>تقرير المصروفات</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; text-align: right; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: right; font-size: 11px; }
        th { background: #e5e7eb; font-weight: bold; }
        .total-row { background: #f3f4f6; font-weight: bold; }
        .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير المصروفات التفصيلي</h1>
        <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>
        @if(isset($filters['date_from']) || isset($filters['date_to']))
        <p>الفترة: من {{ $filters['date_from'] ?? 'البداية' }} إلى {{ $filters['date_to'] ?? 'النهاية' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>التاريخ</th>
                <th>المشروع</th>
                <th>الفئة</th>
                <th>البند</th>
                <th>المبلغ</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                <td>{{ $expense->project->name ?? '-' }}</td>
                <td>{{ $expense->category->name ?? '-' }}</td>
                <td>{{ $expense->item->name ?? '-' }}</td>
                <td>{{ number_format($expense->amount, 2) }}</td>
                <td>{{ $expense->status == 'approved' ? 'معتمد' : 'معلق' }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5">الإجمالي</td>
                <td colspan="2">{{ number_format($expenses->sum('amount'), 2) }} ريال</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>نظام إدارة مصروفات الإنتاج الفني (PEMS)</p>
    </div>
</body>
</html>

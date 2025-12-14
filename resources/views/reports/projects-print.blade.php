@extends('reports.print')

@section('title', 'تقرير المشاريع')
@section('report-title', 'تقرير المشاريع المالي')

@section('content')
<table class="print-table">
    <thead>
        <tr>
            <th>اسم المشروع</th>
            <th>النوع</th>
            <th>الميزانية الكلية</th>
            <th>المصروف</th>
            <th>المتبقي</th>
            <th>نسبة الإنجاز</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalBudget = 0;
            $totalSpent = 0;
        @endphp
        
        @foreach($projects as $project)
            @php
                $totalBudget += $project->total_budget;
                $totalSpent += $project->spent_amount;
                $remaining = $project->total_budget - $project->spent_amount;
                $percentage = $project->total_budget > 0 ? ($project->spent_amount / $project->total_budget) * 100 : 0;
            @endphp
            <tr>
                <td><strong>{{ $project->name }}</strong></td>
                <td>{{ $project->type }}</td>
                <td>{{ number_format($project->total_budget, 0) }}</td>
                <td>{{ number_format($project->spent_amount, 0) }}</td>
                <td>{{ number_format($remaining, 0) }}</td>
                <td>{{ number_format($percentage, 1) }}%</td>
            </tr>
        @endforeach
        
        <tr class="total-row">
            <td colspan="2"><strong>الإجمالي</strong></td>
            <td><strong>{{ number_format($totalBudget, 0) }}</strong></td>
            <td><strong>{{ number_format($totalSpent, 0) }}</strong></td>
            <td><strong>{{ number_format($totalBudget - $totalSpent, 0) }}</strong></td>
            <td><strong>-</strong></td>
        </tr>
    </tbody>
</table>
@endsection
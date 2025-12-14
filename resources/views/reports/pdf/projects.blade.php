@extends('reports.pdf.layout')

@section('title', 'تقرير المشاريع')
@section('report-title', 'تقرير المشاريع')

@section('content')
<table>
    <thead>
        <tr>
            <th>اسم المشروع</th>
            <th>النوع</th>
            <th>الميزانية</th>
            <th>المصروف</th>
            <th>المتبقي</th>
            <th>النسبة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($projects as $project)
        <tr>
            <td>{{ $project->name }}</td>
            <td>
                @if($project->type == 'series') مسلسل
                @elseif($project->type == 'movie') فيلم
                @else برنامج @endif
            </td>
            <td>{{ number_format($project->total_budget) }}</td>
            <td>{{ number_format($project->spent_amount) }}</td>
            <td>{{ number_format($project->remaining_budget) }}</td>
            <td>{{ number_format($project->budget_percentage, 1) }}%</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="2">الإجمالي</td>
            <td>{{ number_format($projects->sum('total_budget')) }}</td>
            <td>{{ number_format($projects->sum('spent_amount')) }}</td>
            <td>{{ number_format($projects->sum('remaining_budget')) }}</td>
            <td>-</td>
        </tr>
    </tbody>
</table>
@endsection

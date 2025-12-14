@extends('reports.pdf.layout')

@section('title', 'تقرير العهد')
@section('report-title', 'تقرير العهد المالية')

@section('content')
<table>
    <thead>
        <tr>
            <th>رقم العهدة</th>
            <th>المشروع</th>
            <th>المبلغ</th>
            <th>الحالة</th>
            <th>التاريخ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($custodies as $custody)
        <tr>
            <td>{{ $custody->custody_number ?? 'C-' . $custody->id }}</td>
            <td>{{ $custody->project->name }}</td>
            <td>{{ number_format($custody->amount) }}</td>
            <td>{{ $custody->status }}</td>
            <td>{{ $custody->created_at->format('Y-m-d') }}</td>
        </tr>
        @endforeach
        <tr class="total">
            <td colspan="2">الإجمالي</td>
            <td>{{ number_format($custodies->sum('amount')) }}</td>
            <td colspan="2">-</td>
        </tr>
    </tbody>
</table>
@endsection

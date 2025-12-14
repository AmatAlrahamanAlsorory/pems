<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustodiesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $custodies;

    public function __construct($custodies)
    {
        $this->custodies = $custodies;
    }

    public function collection()
    {
        return $this->custodies;
    }

    public function headings(): array
    {
        return [
            'رقم العهدة',
            'المشروع',
            'المستلم',
            'المبلغ',
            'المصروف',
            'المتبقي',
            'نسبة التصفية %',
            'الحالة',
            'التاريخ'
        ];
    }

    public function map($custody): array
    {
        $settlementPercentage = $custody->amount > 0 ? ($custody->spent_amount / $custody->amount) * 100 : 0;
        
        return [
            $custody->custody_number,
            $custody->project->name ?? '-',
            $custody->requestedBy->name ?? '-',
            number_format($custody->amount, 2),
            number_format($custody->spent_amount, 2),
            number_format($custody->remaining_amount, 2),
            number_format($settlementPercentage, 1),
            $this->getStatusLabel($custody->status),
            $custody->created_at->format('Y-m-d')
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'requested' => 'مطلوبة',
            'approved' => 'معتمدة',
            'active' => 'نشطة',
            'settling' => 'قيد التصفية',
            'closed' => 'مغلقة',
            'overdue' => 'متأخرة'
        ];
        
        return $labels[$status] ?? $status;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E5E7EB']]],
        ];
    }
}

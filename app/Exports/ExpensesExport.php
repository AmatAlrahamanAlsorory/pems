<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $expenses;
    protected $includeProject;
    protected $includeCategory;

    public function __construct($expenses, $options = [])
    {
        $this->expenses = $expenses;
        $this->includeProject = $options['include_project'] ?? true;
        $this->includeCategory = $options['include_category'] ?? true;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        $headings = ['#', 'التاريخ', 'الوصف', 'المبلغ', 'الحالة'];
        
        if ($this->includeProject) {
            array_splice($headings, 2, 0, ['المشروع']);
        }
        
        if ($this->includeCategory) {
            array_splice($headings, $this->includeProject ? 3 : 2, 0, ['الفئة']);
        }
        
        return $headings;
    }

    public function map($expense): array
    {
        $data = [
            $expense->id,
            $expense->expense_date->format('Y-m-d'),
        ];
        
        if ($this->includeProject) {
            $data[] = $expense->project->name ?? '-';
        }
        
        if ($this->includeCategory) {
            $data[] = $expense->category->name ?? '-';
        }
        
        $data[] = $expense->description;
        $data[] = number_format($expense->amount, 2);
        $data[] = $expense->status == 'approved' ? 'معتمد' : 'معلق';
        
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E5E7EB']]],
        ];
    }
}

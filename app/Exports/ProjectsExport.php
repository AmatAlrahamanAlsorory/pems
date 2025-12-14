<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $projects;

    public function __construct($projects)
    {
        $this->projects = $projects;
    }

    public function collection()
    {
        return $this->projects;
    }

    public function headings(): array
    {
        return [
            'اسم المشروع',
            'النوع', 
            'الميزانية الكلية',
            'المصروف',
            'المتبقي',
            'النسبة %',
            'الحالة'
        ];
    }

    public function map($project): array
    {
        $percentage = $project->total_budget > 0 ? ($project->spent_amount / $project->total_budget) * 100 : 0;
        
        return [
            $project->name,
            $project->type == 'series' ? 'مسلسل' : 'فيلم',
            number_format($project->total_budget, 2),
            number_format($project->spent_amount, 2), 
            number_format($project->total_budget - $project->spent_amount, 2),
            number_format($percentage, 1),
            $project->status
        ];
    }
}
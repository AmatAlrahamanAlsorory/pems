<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class TestCriticalProjects extends Command
{
    protected $signature = 'test:critical-projects {--create : Create test data}';
    protected $description = 'Test critical projects functionality';

    public function handle()
    {
        if ($this->option('create')) {
            $this->createTestData();
        }

        $this->checkCriticalProjects();
    }

    private function createTestData()
    {
        $this->info('Creating test projects...');

        // مشروع عادي
        Project::create([
            'name' => 'مشروع اختبار عادي',
            'type' => 'series',
            'total_budget' => 100000,
            'spent_amount' => 50000,
            'status' => 'active',
            'start_date' => now(),
        ]);

        // مشروع خطر (90%)
        Project::create([
            'name' => 'مشروع اختبار خطر',
            'type' => 'movie',
            'total_budget' => 200000,
            'spent_amount' => 185000,
            'status' => 'active',
            'start_date' => now(),
        ]);

        // مشروع حرج (100%+)
        Project::create([
            'name' => 'مشروع اختبار حرج',
            'type' => 'program',
            'total_budget' => 150000,
            'spent_amount' => 160000,
            'status' => 'active',
            'start_date' => now(),
        ]);

        $this->info('Test data created successfully!');
    }

    private function checkCriticalProjects()
    {
        $this->info('Checking critical projects...');

        $projects = Project::all();
        
        $this->table(
            ['Project', 'Budget', 'Spent', 'Percentage', 'Status'],
            $projects->map(function ($project) {
                return [
                    $project->name,
                    number_format($project->total_budget),
                    number_format($project->spent_amount),
                    number_format($project->budget_percentage, 1) . '%',
                    $project->budget_status
                ];
            })
        );

        $critical = $projects->where('budget_status', 'critical');
        $danger = $projects->where('budget_status', 'danger');

        $this->warn("Critical projects: {$critical->count()}");
        $this->warn("Danger projects: {$danger->count()}");

        if ($critical->count() > 0) {
            $this->error('⚠️  Critical projects found!');
            foreach ($critical as $project) {
                $this->error("- {$project->name}: {$project->budget_percentage}%");
            }
        }
    }
}
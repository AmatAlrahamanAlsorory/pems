<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// جدولة التنبيهات التلقائية
Schedule::command('custody:check-alerts')->hourly();
Schedule::command('budget:check-alerts')->everyThirtyMinutes();

// تقارير يومية
Schedule::command('report:daily')->dailyAt('08:00');
Schedule::command('report:daily')->dailyAt('17:00');

// فحص العهد المتأخرة
Schedule::command('custody:check-alerts')->dailyAt('09:00');
Schedule::command('custody:check-alerts')->dailyAt('14:00');
Schedule::command('custody:check-alerts')->dailyAt('18:00');

// تحديث الميزانيات الدورية
Schedule::call(function () {
    $activeProjects = \App\Models\Project::where('status', 'active')->get();
    foreach ($activeProjects as $project) {
        $activeBudget = $project->active_periodic_budget;
        if ($activeBudget) {
            app(\App\Http\Controllers\PeriodicBudgetController::class)
                ->updateSpending($project, $activeBudget);
        }
    }
})->hourly();

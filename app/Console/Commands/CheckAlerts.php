<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlertService;

class CheckAlerts extends Command
{
    protected $signature = 'alerts:check';
    protected $description = 'فحص جميع التنبيهات (الميزانيات والعهد المتأخرة)';

    public function handle()
    {
        $this->info('بدء فحص التنبيهات...');
        
        $alertService = app(AlertService::class);
        $alertService->checkAllAlerts();
        
        $this->info('تم فحص التنبيهات بنجاح!');
        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CustodyRulesService;

class CheckWeeklySettlement extends Command
{
    protected $signature = 'custody:check-weekly';
    protected $description = 'فحص العهد التي تحتاج تصفية أسبوعية';

    public function handle()
    {
        $this->info('بدء فحص التصفية الأسبوعية...');
        
        $custodyRules = app(CustodyRulesService::class);
        $custodyRules->checkWeeklySettlement();
        
        $this->info('تم فحص التصفية الأسبوعية بنجاح!');
        return 0;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Custody;
use App\Models\Project;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CustodiesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('💰 إضافة عهد جديدة...');
        
        $projects = Project::all();
        $locations = Location::all();
        $users = User::all();
        
        if ($projects->isEmpty() || $users->isEmpty()) {
            $this->command->error('❌ لا توجد مشاريع أو مستخدمين');
            return;
        }
        
        $existingCustodies = Custody::count();
        $this->command->info("ℹ️ يوجد حالياً {$existingCustodies} عهدة - سيتم إضافة عهد جديدة");
        
        $this->createAdditionalCustodies($projects, $locations, $users, $existingCustodies);
        
        $this->command->info('✅ تم إضافة العهد الجديدة بنجاح!');
    }
    
    private function createAdditionalCustodies($projects, $locations, $users, $existingCount)
    {
        $newCustodies = [
            [
                'amount' => 250000,
                'purpose' => 'مصروفات تصوير المشاهد الخارجية',
                'status' => 'requested'
            ],
            [
                'amount' => 180000,
                'purpose' => 'شراء معدات صوت متقدمة',
                'status' => 'active'
            ],
            [
                'amount' => 320000,
                'purpose' => 'أجور الممثلين الرئيسيين للحلقات الأخيرة',
                'status' => 'active'
            ],
            [
                'amount' => 95000,
                'purpose' => 'مصروفات السفر لموقع التصوير في أبها',
                'status' => 'requested'
            ],
            [
                'amount' => 140000,
                'purpose' => 'تكاليف الديكور والأزياء الإضافية',
                'status' => 'active'
            ],
            [
                'amount' => 75000,
                'purpose' => 'مصروفات الطعام والضيافة للطاقم',
                'status' => 'settled'
            ],
            [
                'amount' => 200000,
                'purpose' => 'معدات إضاءة خاصة للمشاهد الليلية',
                'status' => 'requested'
            ],
            [
                'amount' => 110000,
                'purpose' => 'تكاليف المونتاج والمؤثرات البصرية',
                'status' => 'active'
            ]
        ];
        
        foreach ($newCustodies as $index => $custodyData) {
            // إنشاء رقم عهدة فريد
            do {
                $custodyNumber = $existingCount + $index + 1 + rand(100, 999);
                $custodyNumberStr = 'CUST-2025-' . str_pad($custodyNumber, 3, '0', STR_PAD_LEFT);
            } while (Custody::where('custody_number', $custodyNumberStr)->exists());
            
            Custody::create([
                'custody_number' => $custodyNumberStr,
                'project_id' => $projects->random()->id,
                'location_id' => $locations->isNotEmpty() ? $locations->random()->id : 1,
                'requested_by' => $users->random()->id,
                'amount' => $custodyData['amount'],
                'remaining_amount' => $custodyData['amount'],
                'purpose' => $custodyData['purpose'],
                'status' => $custodyData['status'],
                'request_date' => Carbon::now()->subDays(rand(1, 45)),
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => Carbon::now()->subDays(rand(0, 15))
            ]);
        }
    }
}
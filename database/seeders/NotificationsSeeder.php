<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Approval;
use App\Models\User;
use App\Models\Project;
use App\Models\Custody;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔔 إضافة الإشعارات والموافقات...');
        
        $users = User::all();
        $projects = Project::all();
        $custodies = Custody::all();
        $expenses = Expense::all();
        
        if ($users->isEmpty()) {
            $this->command->error('❌ لا توجد مستخدمين');
            return;
        }
        
        $this->createNotifications($users, $projects, $custodies, $expenses);
        $this->createApprovals($users, $custodies, $expenses);
        
        $this->command->info('✅ تم إضافة الإشعارات والموافقات بنجاح!');
    }
    
    private function createNotifications($users, $projects, $custodies, $expenses)
    {
        // فحص إذا كانت هناك إشعارات موجودة مسبقاً
        $existingNotifications = Notification::count();
        if ($existingNotifications > 0) {
            $this->command->info('ℹ️ توجد إشعارات موجودة مسبقاً - سيتم إضافة إشعارات جديدة فقط');
        }
        
        $notifications = [
            [
                'title' => 'مصروف جديد يحتاج موافقة',
                'message' => 'تم تسجيل مصروف جديد بقيمة 45,000 ر.س ويحتاج موافقتك',
                'level' => 'warning',
                'type' => 'expense_approval',
                'data' => json_encode(['expense_id' => $expenses->random()->id ?? 1, 'amount' => 45000])
            ],
            [
                'title' => 'تحذير ميزانية مشروع',
                'message' => 'مشروع أساطير الصحراء تجاوز 85% من الميزانية المخصصة',
                'level' => 'danger',
                'type' => 'budget_warning',
                'data' => json_encode(['project_id' => $projects->random()->id ?? 1, 'percentage' => 85])
            ],
            [
                'title' => 'عهدة جديدة مطلوبة',
                'message' => 'تم طلب عهدة جديدة من أحمد الشمري بقيمة 75,000 ر.س',
                'level' => 'info',
                'type' => 'custody_request',
                'data' => json_encode(['custody_id' => $custodies->random()->id ?? 1, 'amount' => 75000])
            ],
            [
                'title' => 'تجاوز ميزانية مشروع',
                'message' => 'مشروع حديث المملكة تجاوز الميزانية المحددة بنسبة 5%',
                'level' => 'critical',
                'type' => 'budget_exceeded',
                'data' => json_encode(['project_id' => $projects->random()->id ?? 1, 'percentage' => 105])
            ],
            [
                'title' => 'موافقة مطلوبة',
                'message' => 'عهدة بقيمة 85,000 ر.س تحتاج موافقة عاجلة',
                'level' => 'warning',
                'type' => 'custody_approval',
                'data' => json_encode(['custody_id' => $custodies->random()->id ?? 1, 'amount' => 85000])
            ],
            [
                'title' => 'مصروف مرفوض',
                'message' => 'تم رفض مصروف بقيمة 25,000 ر.س لعدم توفر الفاتورة',
                'level' => 'danger',
                'type' => 'expense_rejected',
                'data' => json_encode(['expense_id' => $expenses->random()->id ?? 1, 'amount' => 25000])
            ],
            [
                'title' => 'تذكير عهدة',
                'message' => 'عهدة بقيمة 120,000 ر.س لم يتم تسديدها منذ 15 يوم',
                'level' => 'warning',
                'type' => 'custody_reminder',
                'data' => json_encode(['custody_id' => $custodies->random()->id ?? 1, 'days' => 15])
            ],
            [
                'title' => 'مشروع جديد',
                'message' => 'تم إنشاء مشروع جديد: برنامج الثقافة والتراث',
                'level' => 'info',
                'type' => 'project_created',
                'data' => json_encode(['project_id' => $projects->random()->id ?? 1])
            ]
        ];
        
        foreach ($notifications as $index => $notificationData) {
            foreach ($users as $user) {
                // إنشاء إشعارات مختلفة لكل مستخدم
                if ($index % 3 == $user->id % 3) {
                    Notification::create([
                        'user_id' => $user->id,
                        'title' => $notificationData['title'],
                        'message' => $notificationData['message'],
                        'level' => $notificationData['level'],
                        'type' => $notificationData['type'],
                        'data' => $notificationData['data'],
                        'is_read' => rand(0, 1) == 1,
                        'created_at' => Carbon::now()->subHours(rand(1, 72))
                    ]);
                }
            }
        }
    }
    
    private function createApprovals($users, $custodies, $expenses)
    {
        // فحص إذا كانت هناك موافقات موجودة مسبقاً
        $existingApprovals = Approval::count();
        if ($existingApprovals > 0) {
            $this->command->info('ℹ️ توجد موافقات موجودة مسبقاً - سيتم إضافة موافقات جديدة فقط');
        }
        
        // إنشاء موافقات للعهد (فقط إذا لم تكن موجودة)
        $custodiesWithoutApprovals = $custodies->filter(function($custody) {
            return !Approval::where('approvable_type', 'App\\Models\\Custody')
                          ->where('approvable_id', $custody->id)
                          ->exists();
        });
        
        foreach ($custodiesWithoutApprovals->take(3) as $custody) {
            Approval::create([
                'approvable_type' => 'App\\Models\\Custody',
                'approvable_id' => $custody->id,
                'user_id' => $users->where('role', 'financial_manager')->first()->id ?? 1,
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(rand(1, 48))
            ]);
        }
        
        // إنشاء موافقات للمصروفات (فقط إذا لم تكن موجودة)
        $expensesWithoutApprovals = $expenses->where('status', 'pending')->filter(function($expense) {
            return !Approval::where('approvable_type', 'App\\Models\\Expense')
                          ->where('approvable_id', $expense->id)
                          ->exists();
        });
        
        foreach ($expensesWithoutApprovals->take(2) as $expense) {
            Approval::create([
                'approvable_type' => 'App\\Models\\Expense',
                'approvable_id' => $expense->id,
                'user_id' => $users->where('role', 'admin_accountant')->first()->id ?? 1,
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(rand(1, 24))
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationsDemoSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('لا توجد مستخدمين في قاعدة البيانات');
            return;
        }

        $notifications = [
            [
                'title' => 'تحذير: تجاوز الميزانية',
                'message' => 'مشروع "فيلم وثائقي - تراث السعودية" تجاوز 85% من الميزانية المخصصة',
                'level' => 'warning',
                'type' => 'budget_warning'
            ],
            [
                'title' => 'عهدة تحتاج موافقة',
                'message' => 'عهدة جديدة بقيمة 25,000 ر.س تحتاج موافقتك',
                'level' => 'info',
                'type' => 'custody_approval'
            ],
            [
                'title' => 'مصروف مرفوض',
                'message' => 'تم رفض مصروف "معدات إضاءة" بقيمة 15,000 ر.س - السبب: تجاوز الميزانية المخصصة',
                'level' => 'danger',
                'type' => 'expense_rejected'
            ],
            [
                'title' => 'تذكير: انتهاء مهلة المشروع',
                'message' => 'مشروع "فيلم وثائقي - تراث السعودية" ينتهي خلال 15 يوم',
                'level' => 'warning',
                'type' => 'project_deadline'
            ],
            [
                'title' => 'تحديث النظام',
                'message' => 'تم تحديث نظام إدارة المصروفات إلى الإصدار 2.1 بنجاح',
                'level' => 'info',
                'type' => 'system_update'
            ],
            [
                'title' => 'تنبيه أمني',
                'message' => 'تم اكتشاف محاولة دخول غير مصرح بها من عنوان IP غير معروف',
                'level' => 'critical',
                'type' => 'security_alert'
            ]
        ];

        foreach ($users as $user) {
            foreach ($notifications as $index => $notificationData) {
                // إنشاء بعض الإشعارات كمقروءة وبعضها غير مقروء
                $isRead = $index % 3 == 0; // كل ثالث إشعار يكون مقروء
                
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $notificationData['title'],
                    'message' => $notificationData['message'],
                    'level' => $notificationData['level'],
                    'type' => $notificationData['type'],
                    'is_read' => $isRead,
                    'created_at' => now()->subHours(rand(1, 48))
                ]);
            }
        }

        $this->command->info('تم إنشاء الإشعارات التجريبية بنجاح');
    }
}
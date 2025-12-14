<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Notification;
use App\Models\Project;
use Illuminate\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@pems.com')->first();
        $projects = Project::limit(3)->get();

        $notifications = [
            [
                'user_id' => $admin->id,
                'type' => 'budget_alert',
                'title' => 'تجاوز الميزانية',
                'message' => 'مشروع ' . $projects[0]->name . ' تجاوز 85% من الميزانية',
                'level' => 'warning',
                'is_read' => false,
            ],
            [
                'user_id' => $admin->id,
                'type' => 'custody_alert',
                'title' => 'عهدة قاربت على الانتهاء',
                'message' => 'العهدة CUST-2025-101 متبقي منها 30% فقط',
                'level' => 'danger',
                'is_read' => false,
            ],
            [
                'user_id' => $admin->id,
                'type' => 'expense_created',
                'title' => 'مصروف جديد',
                'message' => 'تم إضافة مصروف جديد بقيمة 85,000 ريال',
                'level' => 'info',
                'is_read' => false,
            ],
            [
                'user_id' => $admin->id,
                'type' => 'budget_critical',
                'title' => 'تنبيه ميزانية حرجة',
                'message' => 'مشروع ' . $projects[1]->name . ' وصل إلى 90% من الميزانية',
                'level' => 'critical',
                'is_read' => false,
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }

        echo "✅ تم إضافة " . count($notifications) . " إشعارات\n";
    }
}

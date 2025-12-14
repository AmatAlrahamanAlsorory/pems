<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersWithRolesSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'أحمد المدير المالي',
                'email' => 'financial.manager@pems.com',
                'password' => Hash::make('password123'),
                'role' => 'financial_manager',
                'location' => 'المقر الرئيسي - صنعاء',
                'email_verified_at' => now()
            ],
            [
                'name' => 'فاطمة محاسب الإدارة',
                'email' => 'admin.accountant@pems.com',
                'password' => Hash::make('password123'),
                'role' => 'admin_accountant',
                'location' => 'المقر الرئيسي - صنعاء',
                'email_verified_at' => now()
            ],
            [
                'name' => 'محمد مدير الإنتاج',
                'email' => 'production.manager@pems.com',
                'password' => Hash::make('password123'),
                'role' => 'production_manager',
                'location' => 'موقع التصوير - استوديو صنعاء',
                'email_verified_at' => now()
            ],
            [
                'name' => 'سارة المحاسب الميداني',
                'email' => 'field.accountant@pems.com',
                'password' => Hash::make('password123'),
                'role' => 'field_accountant',
                'location' => 'موقع التصوير - استوديو صنعاء',
                'email_verified_at' => now()
            ],
            [
                'name' => 'علي المساعد المالي',
                'email' => 'financial.assistant@pems.com',
                'password' => Hash::make('password123'),
                'role' => 'financial_assistant',
                'location' => 'موقع التصوير - عدن',
                'email_verified_at' => now()
            ]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('تم إنشاء المستخدمين بأدوارهم المختلفة:');
        $this->command->info('1. المدير المالي: financial.manager@pems.com');
        $this->command->info('2. محاسب الإدارة: admin.accountant@pems.com');
        $this->command->info('3. مدير الإنتاج: production.manager@pems.com');
        $this->command->info('4. المحاسب الميداني: field.accountant@pems.com');
        $this->command->info('5. المساعد المالي: financial.assistant@pems.com');
        $this->command->info('كلمة المرور لجميع الحسابات: password123');
    }
}
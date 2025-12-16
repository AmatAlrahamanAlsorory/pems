<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'المدير العام',
                'email' => 'admin@pems.com',
                'password' => Hash::make('admin123'),
                'role' => 'financial_manager',
                'location' => 'المقر الرئيسي'
            ],
            [
                'name' => 'المدير المالي العام',
                'email' => 'financial@pems.com',
                'password' => Hash::make('password'),
                'role' => 'financial_manager',
                'location' => 'المقر الرئيسي'
            ],
            [
                'name' => 'محاسب الإدارة',
                'email' => 'accountant@pems.com',
                'password' => Hash::make('password'),
                'role' => 'admin_accountant',
                'location' => 'المقر الرئيسي'
            ],
            [
                'name' => 'مدير إنتاج الموقع',
                'email' => 'production@pems.com',
                'password' => Hash::make('password'),
                'role' => 'production_manager',
                'location' => 'موقع الرياض'
            ],
            [
                'name' => 'المحاسب الميداني',
                'email' => 'field@pems.com',
                'password' => Hash::make('password'),
                'role' => 'field_accountant',
                'location' => 'موقع الرياض'
            ],
            [
                'name' => 'مساعد مالي',
                'email' => 'assistant@pems.com',
                'password' => Hash::make('password'),
                'role' => 'financial_assistant',
                'location' => 'موقع جدة'
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
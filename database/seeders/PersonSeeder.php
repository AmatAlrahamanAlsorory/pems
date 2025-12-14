<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        $people = [
            // ممثلين
            ['name' => 'أحمد السقا', 'type' => 'actor', 'phone' => '0501234567'],
            ['name' => 'منى زكي', 'type' => 'actor', 'phone' => '0507654321'],
            ['name' => 'محمد هنيدي', 'type' => 'actor', 'phone' => '0509876543'],
            
            // فنيين
            ['name' => 'علي المصور', 'type' => 'technician', 'phone' => '0502468135'],
            ['name' => 'سارة المونتاج', 'type' => 'technician', 'phone' => '0508642097'],
            ['name' => 'خالد الصوت', 'type' => 'technician', 'phone' => '0503691470'],
            
            // طاقم
            ['name' => 'فاطمة المكياج', 'type' => 'crew', 'phone' => '0505555555'],
            ['name' => 'محمود الإضاءة', 'type' => 'crew', 'phone' => '0506666666'],
            ['name' => 'نور الأزياء', 'type' => 'crew', 'phone' => '0507777777'],
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
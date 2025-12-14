<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 100, 'name' => 'مصروفات الممثلين والفنانين', 'name_en' => 'Actors & Artists Expenses', 'color' => '#9333EA'],
            ['code' => 200, 'name' => 'مصروفات الطعام والضيافة', 'name_en' => 'Food & Hospitality', 'color' => '#F97316'],
            ['code' => 300, 'name' => 'مصروفات النقل والمواصلات', 'name_en' => 'Transportation', 'color' => '#3B82F6'],
            ['code' => 400, 'name' => 'مصروفات المواقع والديكور', 'name_en' => 'Locations & Decor', 'color' => '#EC4899'],
            ['code' => 500, 'name' => 'مصروفات المعدات والتقنية', 'name_en' => 'Equipment & Technology', 'color' => '#1E40AF'],
            ['code' => 600, 'name' => 'مصروفات الأزياء والمكياج', 'name_en' => 'Costumes & Makeup', 'color' => '#DB2777'],
            ['code' => 700, 'name' => 'مصروفات الطاقم الفني', 'name_en' => 'Technical Crew', 'color' => '#10B981'],
            ['code' => 800, 'name' => 'مصروفات إدارية وتشغيلية', 'name_en' => 'Administrative', 'color' => '#6B7280'],
            ['code' => 900, 'name' => 'مصروفات طوارئ ومتنوعة', 'name_en' => 'Emergency & Miscellaneous', 'color' => '#EF4444'],
        ];

        foreach ($categories as $category) {
            DB::table('expense_categories')->insert([
                'code' => $category['code'],
                'name' => $category['name'],
                'name_en' => $category['name_en'],
                'color' => $category['color'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // البنود الفرعية
        $items = [
            // 100 - مصروفات الممثلين
            ['category_code' => 100, 'code' => 101, 'name' => 'بدلات يومية للممثلين', 'name_en' => 'Daily Allowances', 'requires_invoice' => false, 'approval' => 'automatic'],
            ['category_code' => 100, 'code' => 102, 'name' => 'مواصلات الممثلين', 'name_en' => 'Transportation', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 100, 'code' => 103, 'name' => 'إقامة وسكن الممثلين', 'name_en' => 'Accommodation', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 100, 'code' => 104, 'name' => 'وجبات خاصة للممثلين', 'name_en' => 'Special Meals', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 100, 'code' => 105, 'name' => 'مستلزمات شخصية طارئة', 'name_en' => 'Personal Supplies', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 100, 'code' => 106, 'name' => 'أجور إضافية / ساعات عمل إضافية', 'name_en' => 'Overtime', 'requires_invoice' => false, 'approval' => 'production_manager'],
            ['category_code' => 100, 'code' => 109, 'name' => 'مصروفات ممثلين أخرى', 'name_en' => 'Other Actors Expenses', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 200 - مصروفات الطعام
            ['category_code' => 200, 'code' => 201, 'name' => 'وجبات الفطور للطاقم', 'name_en' => 'Breakfast', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 200, 'code' => 202, 'name' => 'وجبات الغداء للطاقم', 'name_en' => 'Lunch', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 200, 'code' => 203, 'name' => 'وجبات العشاء للطاقم', 'name_en' => 'Dinner', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 200, 'code' => 204, 'name' => 'مشروبات ومرطبات', 'name_en' => 'Beverages', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 200, 'code' => 205, 'name' => 'وجبات خفيفة (سناكس)', 'name_en' => 'Snacks', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 200, 'code' => 206, 'name' => 'ضيافة زوار / ضيوف', 'name_en' => 'Guest Hospitality', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 200, 'code' => 209, 'name' => 'مصروفات طعام أخرى', 'name_en' => 'Other Food Expenses', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 300 - مصروفات النقل
            ['category_code' => 300, 'code' => 301, 'name' => 'وقود السيارات', 'name_en' => 'Fuel', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 300, 'code' => 302, 'name' => 'إيجار سيارات', 'name_en' => 'Car Rental', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 300, 'code' => 303, 'name' => 'أجور سائقين', 'name_en' => 'Driver Fees', 'requires_invoice' => false, 'approval' => 'automatic'],
            ['category_code' => 300, 'code' => 304, 'name' => 'نقل معدات', 'name_en' => 'Equipment Transport', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 300, 'code' => 305, 'name' => 'تذاكر سفر', 'name_en' => 'Travel Tickets', 'requires_invoice' => true, 'approval' => 'management'],
            ['category_code' => 300, 'code' => 306, 'name' => 'صيانة سيارات', 'name_en' => 'Vehicle Maintenance', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 300, 'code' => 309, 'name' => 'مصروفات نقل أخرى', 'name_en' => 'Other Transport', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 400 - مصروفات المواقع
            ['category_code' => 400, 'code' => 401, 'name' => 'إيجار مواقع تصوير', 'name_en' => 'Location Rental', 'requires_invoice' => true, 'approval' => 'management'],
            ['category_code' => 400, 'code' => 402, 'name' => 'تجهيز وإعداد المواقع', 'name_en' => 'Location Setup', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 400, 'code' => 403, 'name' => 'مواد ديكور', 'name_en' => 'Decor Materials', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 400, 'code' => 404, 'name' => 'أثاث وإكسسوارات', 'name_en' => 'Furniture & Accessories', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 400, 'code' => 405, 'name' => 'إضاءة الموقع', 'name_en' => 'Location Lighting', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 400, 'code' => 406, 'name' => 'تنظيف وصيانة الموقع', 'name_en' => 'Cleaning & Maintenance', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 400, 'code' => 409, 'name' => 'مصروفات مواقع أخرى', 'name_en' => 'Other Location Expenses', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 500 - مصروفات المعدات
            ['category_code' => 500, 'code' => 501, 'name' => 'إيجار كاميرات', 'name_en' => 'Camera Rental', 'requires_invoice' => true, 'approval' => 'management'],
            ['category_code' => 500, 'code' => 502, 'name' => 'إيجار معدات إضاءة', 'name_en' => 'Lighting Equipment', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 500, 'code' => 503, 'name' => 'إيجار معدات صوت', 'name_en' => 'Sound Equipment', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 500, 'code' => 504, 'name' => 'مستلزمات تصوير', 'name_en' => 'Filming Supplies', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 500, 'code' => 505, 'name' => 'صيانة معدات', 'name_en' => 'Equipment Maintenance', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 500, 'code' => 509, 'name' => 'مصروفات معدات أخرى', 'name_en' => 'Other Equipment', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 600 - مصروفات الأزياء
            ['category_code' => 600, 'code' => 601, 'name' => 'شراء أزياء', 'name_en' => 'Costume Purchase', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 600, 'code' => 602, 'name' => 'إيجار أزياء', 'name_en' => 'Costume Rental', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 600, 'code' => 603, 'name' => 'مستلزمات مكياج', 'name_en' => 'Makeup Supplies', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 600, 'code' => 604, 'name' => 'مستلزمات شعر وتسريحات', 'name_en' => 'Hair Supplies', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 600, 'code' => 605, 'name' => 'غسيل وتنظيف أزياء', 'name_en' => 'Costume Cleaning', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 600, 'code' => 609, 'name' => 'مصروفات أزياء أخرى', 'name_en' => 'Other Costume Expenses', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 700 - مصروفات الطاقم
            ['category_code' => 700, 'code' => 701, 'name' => 'أجور يومية للفنيين', 'name_en' => 'Daily Wages', 'requires_invoice' => false, 'approval' => 'production_manager'],
            ['category_code' => 700, 'code' => 702, 'name' => 'ساعات إضافية', 'name_en' => 'Overtime', 'requires_invoice' => false, 'approval' => 'production_manager'],
            ['category_code' => 700, 'code' => 703, 'name' => 'بدلات ميدانية', 'name_en' => 'Field Allowances', 'requires_invoice' => false, 'approval' => 'automatic'],
            ['category_code' => 700, 'code' => 704, 'name' => 'إقامة الطاقم', 'name_en' => 'Crew Accommodation', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 700, 'code' => 709, 'name' => 'مصروفات طاقم أخرى', 'name_en' => 'Other Crew Expenses', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 800 - مصروفات إدارية
            ['category_code' => 800, 'code' => 801, 'name' => 'اتصالات وإنترنت', 'name_en' => 'Communications', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 800, 'code' => 802, 'name' => 'قرطاسية ومستلزمات مكتبية', 'name_en' => 'Stationery', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 800, 'code' => 803, 'name' => 'طباعة سيناريو ومستندات', 'name_en' => 'Printing', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 800, 'code' => 804, 'name' => 'تصاريح وتراخيص', 'name_en' => 'Permits & Licenses', 'requires_invoice' => true, 'approval' => 'management'],
            ['category_code' => 800, 'code' => 805, 'name' => 'تأمينات', 'name_en' => 'Insurance', 'requires_invoice' => true, 'approval' => 'management'],
            ['category_code' => 800, 'code' => 806, 'name' => 'رسوم حكومية', 'name_en' => 'Government Fees', 'requires_invoice' => true, 'approval' => 'management'],
            ['category_code' => 800, 'code' => 809, 'name' => 'مصروفات إدارية أخرى', 'name_en' => 'Other Admin Expenses', 'requires_invoice' => true, 'approval' => 'production_manager'],

            // 900 - مصروفات طوارئ
            ['category_code' => 900, 'code' => 901, 'name' => 'مصروفات طوارئ طبية', 'name_en' => 'Medical Emergency', 'requires_invoice' => true, 'approval' => 'automatic'],
            ['category_code' => 900, 'code' => 902, 'name' => 'إصلاحات طارئة', 'name_en' => 'Emergency Repairs', 'requires_invoice' => true, 'approval' => 'production_manager'],
            ['category_code' => 900, 'code' => 903, 'name' => 'تعويضات ومخالفات', 'name_en' => 'Compensations & Fines', 'requires_invoice' => true, 'approval' => 'management'],
            ['category_code' => 900, 'code' => 904, 'name' => 'هدايا وإكراميات', 'name_en' => 'Gifts & Tips', 'requires_invoice' => false, 'approval' => 'production_manager'],
            ['category_code' => 900, 'code' => 909, 'name' => 'مصروفات متنوعة أخرى', 'name_en' => 'Other Miscellaneous', 'requires_invoice' => true, 'approval' => 'production_manager'],
        ];

        foreach ($items as $item) {
            $categoryId = DB::table('expense_categories')->where('code', $item['category_code'])->value('id');
            
            DB::table('expense_items')->insert([
                'expense_category_id' => $categoryId,
                'code' => $item['code'],
                'name' => $item['name'],
                'name_en' => $item['name_en'],
                'requires_invoice' => $item['requires_invoice'],
                'approval_level' => $item['approval'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

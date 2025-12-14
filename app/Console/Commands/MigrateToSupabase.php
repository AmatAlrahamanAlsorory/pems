<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateToSupabase extends Command
{
    protected $signature = 'migrate:supabase {action=export : create|export|clean|test}';
    protected $description = 'تصدير البيانات إلى Supabase';

    public function handle()
    {
        $action = $this->argument('action');
        
        // إعداد اتصال Supabase
        $this->setupSupabaseConnection();

        switch ($action) {
            case 'test':
                $this->testConnection();
                break;
            case 'create':
                $this->createTables();
                break;
            case 'clean':
                $this->cleanTables();
                break;
            case 'export':
            default:
                $this->exportData();
                break;
        }
    }

    private function setupSupabaseConnection()
    {
        config(['database.connections.supabase' => [
            'driver' => 'pgsql',
            'host' => 'aws-0-eu-central-1.pooler.supabase.com',
            'port' => '6543',
            'database' => 'postgres',
            'username' => 'postgres',
            'password' => 'HquTTQSri8Ln3O1R',
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'require',
        ]]);
    }

    private function testConnection()
    {
        $this->info('🔍 اختبار الاتصال بـ Supabase...');
        
        try {
            $result = DB::select('SELECT version()');
            $this->info('✅ تم الاتصال بنجاح!');
            $this->info('إصدار PostgreSQL: ' . $result[0]->version);
            
            // اختبار الجداول
            $tables = DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_type = 'BASE TABLE'
            ");
            
            $this->info('الجداول الموجودة: ' . count($tables));
            foreach ($tables as $table) {
                $this->line('  - ' . $table->table_name);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ فشل الاتصال: ' . $e->getMessage());
            $this->info('تأكد من إعدادات قاعدة البيانات في ملف .env');
        }
    }

    private function createTables()
    {
        $this->info('🔧 إنشاء الجداول في Supabase...');
        
        try {
            // تشغيل migrations على Supabase
            $this->call('migrate', [
                '--database' => 'supabase',
                '--force' => true
            ]);
            
            $this->info('✅ تم إنشاء الجداول بنجاح!');
        } catch (\Exception $e) {
            $this->error('❌ خطأ في إنشاء الجداول: ' . $e->getMessage());
        }
    }

    private function exportData()
    {
        $this->info('🚀 بدء تصدير البيانات إلى Supabase...');
        
        // التحقق من الاتصال أولاً
        try {
            DB::getPdo();
        } catch (\Exception $e) {
            $this->error('❌ فشل الاتصال بـ Supabase: ' . $e->getMessage());
            return;
        }

        $tables = [
            'expense_categories' => 'فئات المصروفات',
            'projects' => 'المشاريع', 
            'locations' => 'المواقع',
            'expense_items' => 'بنود المصروفات',
            'custodies' => 'العهد',
            'expenses' => 'المصروفات',
            'budget_allocations' => 'توزيع الميزانيات'
        ];

        $totalTables = count($tables);
        $bar = $this->output->createProgressBar($totalTables);
        $bar->start();

        $totalRecords = 0;
        foreach ($tables as $table => $arabicName) {
            $count = $this->exportTable($table, $arabicName);
            $totalRecords += $count;
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ تم تصدير {$totalRecords} سجل من {$totalTables} جداول بنجاح!");
        
        // عرض ملخص
        $this->showExportSummary();
    }

    private function exportTable($table, $arabicName)
    {
        try {
            // التحقق من وجود الجدول في قاعدة البيانات المحلية
            if (!Schema::hasTable($table)) {
                $this->newLine();
                $this->warn("⚠️ الجدول {$table} ({$arabicName}) غير موجود في قاعدة البيانات المحلية");
                return 0;
            }

            // التحقق من وجود الجدول في Supabase
            $supabaseTableExists = DB::select("
                SELECT EXISTS (
                    SELECT FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                    AND table_name = ?
                )
            ", [$table]);

            if (!$supabaseTableExists[0]->exists) {
                $this->newLine();
                $this->warn("⚠️ الجدول {$table} غير موجود في Supabase");
                return 0;
            }

            // جلب البيانات من قاعدة البيانات المحلية
            $data = DB::table($table)->get();
            
            if ($data->isEmpty()) {
                return 0;
            }

            // حذف البيانات الموجودة في Supabase (اختياري)
            DB::table($table)->truncate();

            // تحويل البيانات وإدراجها في Supabase
            $chunks = $data->chunk(50); // تقسيم البيانات لتجنب مشاكل الذاكرة
            
            foreach ($chunks as $chunk) {
                $chunkArray = $chunk->map(function ($item) {
                    $array = (array) $item;
                    
                    // تحويل التواريخ إلى تنسيق PostgreSQL
                    foreach ($array as $key => $value) {
                        if ($value && (str_contains($key, '_at') || str_contains($key, '_date'))) {
                            try {
                                $array[$key] = \Carbon\Carbon::parse($value)->toDateTimeString();
                            } catch (\Exception $e) {
                                // إبقاء القيمة كما هي إذا فشل التحويل
                            }
                        }
                    }
                    
                    return $array;
                })->toArray();

                DB::table($table)->insert($chunkArray);
            }

            return $data->count();

        } catch (\Exception $e) {
            $this->newLine();
            $this->error("❌ خطأ في تصدير جدول {$table} ({$arabicName}): " . $e->getMessage());
            return 0;
        }
    }

    private function showExportSummary()
    {
        $this->info("\n📊 ملخص التصدير:");
        $this->info("================");

        try {
            $tables = [
                'expense_categories' => 'فئات المصروفات',
                'projects' => 'المشاريع',
                'locations' => 'المواقع', 
                'expense_items' => 'بنود المصروفات',
                'custodies' => 'العهد',
                'expenses' => 'المصروفات',
                'budget_allocations' => 'توزيع الميزانيات'
            ];

            foreach ($tables as $table => $arabicName) {
                try {
                    $count = DB::table($table)->count();
                    $this->info("✅ {$arabicName}: {$count} سجل");
                } catch (\Exception $e) {
                    $this->warn("⚠️ {$arabicName}: غير متاح");
                }
            }

        } catch (\Exception $e) {
            $this->error("❌ خطأ في عرض الملخص: " . $e->getMessage());
        }
    }

    private function cleanTables()
    {
        $this->info('🧹 تنظيف الجداول في Supabase...');
        
        if (!$this->confirm('هل أنت متأكد من حذف جميع البيانات؟')) {
            $this->info('تم إلغاء العملية.');
            return;
        }

        $tables = [
            'expenses' => 'المصروفات',
            'custodies' => 'العهد', 
            'expense_items' => 'بنود المصروفات',
            'budget_allocations' => 'توزيع الميزانيات',
            'projects' => 'المشاريع',
            'locations' => 'المواقع',
            'expense_categories' => 'فئات المصروفات'
        ];

        foreach ($tables as $table => $arabicName) {
            try {
                DB::table($table)->truncate();
                $this->info("✅ تم تنظيف جدول: {$arabicName}");
            } catch (\Exception $e) {
                $this->warn("⚠️ خطأ في تنظيف جدول {$arabicName}: " . $e->getMessage());
            }
        }

        $this->info('✅ تم تنظيف جميع الجداول!');
    }
}
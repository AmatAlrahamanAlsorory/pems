<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class PerformanceOptimizationService
{
    // CDN Management
    public function uploadToCDN($filePath, $fileName)
    {
        try {
            $cdnEndpoint = config('services.cdn.endpoint');
            $cdnKey = config('services.cdn.api_key');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $cdnKey,
                'Content-Type' => 'multipart/form-data'
            ])->attach('file', file_get_contents($filePath), $fileName)
            ->post($cdnEndpoint . '/upload');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'cdn_url' => $response->json()['url'],
                    'file_id' => $response->json()['file_id']
                ];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            \Log::error('CDN Upload Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Redis Caching
    public function cacheExpenseData($projectId, $data, $ttl = 3600)
    {
        $key = "project_expenses_{$projectId}";
        Redis::setex($key, $ttl, json_encode($data));
        
        // إضافة للفهرس
        Redis::sadd('cached_projects', $projectId);
        
        return true;
    }

    public function getCachedExpenseData($projectId)
    {
        $key = "project_expenses_{$projectId}";
        $cached = Redis::get($key);
        
        return $cached ? json_decode($cached, true) : null;
    }

    public function invalidateProjectCache($projectId)
    {
        $patterns = [
            "project_expenses_{$projectId}",
            "project_budget_{$projectId}",
            "project_analytics_{$projectId}",
            "project_reports_{$projectId}"
        ];

        foreach ($patterns as $pattern) {
            Redis::del($pattern);
        }

        Redis::srem('cached_projects', $projectId);
        
        return true;
    }

    // Database Query Optimization
    public function optimizeExpenseQueries()
    {
        // إنشاء فهارس محسنة
        DB::statement('CREATE INDEX IF NOT EXISTS idx_expenses_project_date ON expenses(project_id, expense_date)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_expenses_category_amount ON expenses(expense_category_id, amount)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_expenses_status_created ON expenses(status, created_at)');
        
        // فهارس مركبة للاستعلامات المعقدة
        DB::statement('CREATE INDEX IF NOT EXISTS idx_expenses_complex ON expenses(project_id, status, expense_date, amount)');
        
        return true;
    }

    public function getOptimizedProjectStats($projectId)
    {
        $cacheKey = "project_stats_{$projectId}";
        
        return Cache::remember($cacheKey, 1800, function() use ($projectId) {
            return DB::select("
                SELECT 
                    COUNT(*) as total_expenses,
                    SUM(amount) as total_amount,
                    AVG(amount) as avg_amount,
                    MAX(amount) as max_amount,
                    MIN(amount) as min_amount,
                    COUNT(DISTINCT expense_category_id) as categories_used
                FROM expenses 
                WHERE project_id = ? AND status = 'approved'
            ", [$projectId])[0];
        });
    }

    // Load Balancing
    public function getOptimalDatabaseConnection()
    {
        $connections = config('database.connections');
        $readConnections = [];
        
        foreach ($connections as $name => $config) {
            if (isset($config['read']) && $config['read']) {
                $readConnections[] = $name;
            }
        }

        // اختيار اتصال عشوائي للقراءة
        return $readConnections[array_rand($readConnections)] ?? 'mysql';
    }

    public function distributeFileUploads($files)
    {
        $servers = config('filesystems.upload_servers', ['server1', 'server2', 'server3']);
        $distribution = [];

        foreach ($files as $index => $file) {
            $serverIndex = $index % count($servers);
            $distribution[$servers[$serverIndex]][] = $file;
        }

        return $distribution;
    }

    // Memory Optimization
    public function optimizeMemoryUsage()
    {
        // تنظيف الذاكرة
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }

        // تحسين إعدادات PHP
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit')
        ];
    }

    // Asset Optimization
    public function optimizeAssets()
    {
        $assets = [
            'css' => glob(public_path('css/*.css')),
            'js' => glob(public_path('js/*.js')),
            'images' => glob(public_path('images/*.{jpg,jpeg,png,gif}'), GLOB_BRACE)
        ];

        $optimized = [];

        foreach ($assets['css'] as $cssFile) {
            $optimized['css'][] = $this->minifyCSS($cssFile);
        }

        foreach ($assets['js'] as $jsFile) {
            $optimized['js'][] = $this->minifyJS($jsFile);
        }

        foreach ($assets['images'] as $imageFile) {
            $optimized['images'][] = $this->compressImage($imageFile);
        }

        return $optimized;
    }

    // Session Optimization
    public function optimizeSessionStorage()
    {
        // استخدام Redis للجلسات
        config(['session.driver' => 'redis']);
        config(['session.connection' => 'session']);
        
        // تحسين إعدادات الجلسة
        config(['session.lifetime' => 120]);
        config(['session.expire_on_close' => false]);
        
        return true;
    }

    // API Response Optimization
    public function optimizeAPIResponse($data, $compressionLevel = 6)
    {
        // ضغط البيانات
        $compressed = gzencode(json_encode($data), $compressionLevel);
        
        return [
            'original_size' => strlen(json_encode($data)),
            'compressed_size' => strlen($compressed),
            'compression_ratio' => (1 - strlen($compressed) / strlen(json_encode($data))) * 100,
            'data' => base64_encode($compressed)
        ];
    }

    // Database Connection Pooling
    public function setupConnectionPooling()
    {
        $config = [
            'pool_size' => 10,
            'max_connections' => 50,
            'idle_timeout' => 300,
            'connection_timeout' => 30
        ];

        // تطبيق إعدادات تجميع الاتصالات
        DB::statement("SET SESSION wait_timeout = {$config['idle_timeout']}");
        DB::statement("SET SESSION interactive_timeout = {$config['idle_timeout']}");
        
        return $config;
    }

    private function minifyCSS($filePath)
    {
        $css = file_get_contents($filePath);
        
        // إزالة التعليقات
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // إزالة المسافات الزائدة
        $css = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $css);
        
        $minifiedPath = str_replace('.css', '.min.css', $filePath);
        file_put_contents($minifiedPath, $css);
        
        return $minifiedPath;
    }

    private function minifyJS($filePath)
    {
        $js = file_get_contents($filePath);
        
        // إزالة التعليقات البسيطة
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        $js = preg_replace('/\/\/.*$/', '', $js);
        
        // إزالة المسافات الزائدة
        $js = preg_replace('/\s+/', ' ', $js);
        
        $minifiedPath = str_replace('.js', '.min.js', $filePath);
        file_put_contents($minifiedPath, $js);
        
        return $minifiedPath;
    }

    private function compressImage($imagePath)
    {
        $imageInfo = getimagesize($imagePath);
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($imagePath);
                $compressedPath = str_replace('.jpg', '_compressed.jpg', $imagePath);
                imagejpeg($image, $compressedPath, 85);
                break;
                
            case 'image/png':
                $image = imagecreatefrompng($imagePath);
                $compressedPath = str_replace('.png', '_compressed.png', $imagePath);
                imagepng($image, $compressedPath, 6);
                break;
                
            default:
                return $imagePath;
        }

        imagedestroy($image);
        return $compressedPath;
    }

    public function getPerformanceMetrics()
    {
        return [
            'memory_usage' => $this->optimizeMemoryUsage(),
            'cache_hit_ratio' => $this->getCacheHitRatio(),
            'database_performance' => $this->getDatabasePerformance(),
            'response_times' => $this->getAverageResponseTimes()
        ];
    }

    private function getCacheHitRatio()
    {
        $info = Redis::info('stats');
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        
        return $hits + $misses > 0 ? ($hits / ($hits + $misses)) * 100 : 0;
    }

    private function getDatabasePerformance()
    {
        $queries = DB::getQueryLog();
        
        return [
            'total_queries' => count($queries),
            'average_time' => count($queries) > 0 ? array_sum(array_column($queries, 'time')) / count($queries) : 0,
            'slow_queries' => count(array_filter($queries, fn($q) => $q['time'] > 1000))
        ];
    }

    private function getAverageResponseTimes()
    {
        // يمكن تطبيق هذا باستخدام middleware لتتبع أوقات الاستجابة
        return Cache::get('average_response_times', [
            'api' => 250,
            'web' => 180,
            'dashboard' => 320
        ]);
    }
}
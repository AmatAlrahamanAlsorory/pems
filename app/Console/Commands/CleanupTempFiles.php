<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupTempFiles extends Command
{
    protected $signature = 'cleanup:temp-files';
    protected $description = 'Clean up temporary files older than 24 hours';

    public function handle()
    {
        $directories = ['temp-ocr', 'temp-uploads'];
        $cleaned = 0;
        
        foreach ($directories as $directory) {
            if (Storage::exists($directory)) {
                $files = Storage::files($directory);
                
                foreach ($files as $file) {
                    $lastModified = Storage::lastModified($file);
                    
                    if ($lastModified < now()->subDay()->timestamp) {
                        Storage::delete($file);
                        $cleaned++;
                    }
                }
            }
        }
        
        $this->info("Cleaned up {$cleaned} temporary files.");
    }
}
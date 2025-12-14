<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DigitalArchiveService
{
    /**
     * أرشفة فاتورة مع التحسين والتشفير
     */
    public function archiveInvoice($file, $expenseId, $metadata = [])
    {
        // تحسين الصورة
        $optimizedPath = $this->optimizeImage($file);
        
        // استخراج النص (OCR)
        $ocrService = app(OCRService::class);
        $extractedData = $ocrService->extractInvoiceData($optimizedPath);
        
        // تشفير وحفظ
        $encryptionService = app(FileEncryptionService::class);
        $encryptedPath = $encryptionService->storeInvoice($file, $expenseId);
        
        // حفظ البيانات الوصفية
        $archiveData = [
            'expense_id' => $expenseId,
            'file_path' => $encryptedPath,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extracted_data' => $extractedData,
            'metadata' => $metadata,
            'archived_at' => now(),
            'archived_by' => auth()->id()
        ];
        
        \App\Models\InvoiceArchive::create($archiveData);
        
        // حذف الملف المؤقت
        Storage::delete($optimizedPath);
        
        return $encryptedPath;
    }
    
    /**
     * تحسين الصورة (ضغط وتحسين الجودة)
     */
    private function optimizeImage($file)
    {
        $image = Image::make($file);
        
        // تحسين الجودة
        $image->orientate(); // تصحيح الاتجاه
        
        // تحويل إلى أبيض وأسود للفواتير
        if ($this->isInvoice($file)) {
            $image->greyscale();
            $image->contrast(20);
        }
        
        // ضغط الحجم
        if ($image->width() > 2000) {
            $image->resize(2000, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        
        // حفظ مؤقت
        $tempPath = 'temp/optimized_' . time() . '.jpg';
        Storage::put($tempPath, $image->encode('jpg', 85));
        
        return $tempPath;
    }
    
    /**
     * البحث في الأرشيف
     */
    public function searchArchive($query, $filters = [])
    {
        $q = \App\Models\InvoiceArchive::with('expense.project');
        
        // بحث في النص المستخرج
        if ($query) {
            $q->where(function($q) use ($query) {
                $q->where('original_name', 'LIKE', "%{$query}%")
                  ->orWhereRaw("JSON_EXTRACT(extracted_data, '$.vendor') LIKE ?", ["%{$query}%"])
                  ->orWhereRaw("JSON_EXTRACT(extracted_data, '$.invoice_number') LIKE ?", ["%{$query}%"]);
            });
        }
        
        // فلاتر
        if (isset($filters['project_id'])) {
            $q->whereHas('expense', function($q) use ($filters) {
                $q->where('project_id', $filters['project_id']);
            });
        }
        
        if (isset($filters['date_from'])) {
            $q->whereDate('archived_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $q->whereDate('archived_at', '<=', $filters['date_to']);
        }
        
        return $q->paginate(20);
    }
    
    /**
     * تصنيف تلقائي للفواتير
     */
    public function autoClassify($invoiceArchiveId)
    {
        $archive = \App\Models\InvoiceArchive::findOrFail($invoiceArchiveId);
        $extractedData = $archive->extracted_data;
        
        $ocrService = app(OCRService::class);
        
        // اقتراح الفئة
        $suggestedCategory = $ocrService->suggestCategory(
            $extractedData['vendor'] ?? '' . ' ' . 
            $archive->original_name
        );
        
        // اقتراح المشروع (بناءً على الأنماط السابقة)
        $suggestedProject = $this->suggestProject($extractedData);
        
        return [
            'suggested_category' => $suggestedCategory,
            'suggested_project' => $suggestedProject,
            'confidence' => $extractedData['confidence'] ?? 0
        ];
    }
    
    /**
     * اقتراح المشروع
     */
    private function suggestProject($extractedData)
    {
        // البحث عن مصروفات سابقة من نفس المورد
        if (isset($extractedData['vendor'])) {
            $previousExpense = Expense::where('vendor_name', 'LIKE', "%{$extractedData['vendor']}%")
                ->latest()
                ->first();
            
            if ($previousExpense) {
                return $previousExpense->project_id;
            }
        }
        
        return null;
    }
    
    /**
     * إحصائيات الأرشيف
     */
    public function getArchiveStats()
    {
        return [
            'total_invoices' => \App\Models\InvoiceArchive::count(),
            'total_size' => \App\Models\InvoiceArchive::sum('file_size'),
            'by_project' => \App\Models\InvoiceArchive::with('expense.project')
                ->get()
                ->groupBy('expense.project.name')
                ->map->count(),
            'by_month' => \App\Models\InvoiceArchive::selectRaw('MONTH(archived_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->pluck('count', 'month'),
            'recent' => \App\Models\InvoiceArchive::with('expense')
                ->latest()
                ->limit(10)
                ->get()
        ];
    }
    
    /**
     * تنظيف الأرشيف (حذف الملفات القديمة)
     */
    public function cleanupOldArchives($olderThanYears = 5)
    {
        $archives = \App\Models\InvoiceArchive::where('archived_at', '<', now()->subYears($olderThanYears))
            ->get();
        
        $deleted = 0;
        
        foreach ($archives as $archive) {
            if (Storage::exists($archive->file_path)) {
                Storage::delete($archive->file_path);
                $archive->delete();
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    /**
     * تصدير الأرشيف
     */
    public function exportArchive($projectId, $format = 'zip')
    {
        $archives = \App\Models\InvoiceArchive::whereHas('expense', function($q) use ($projectId) {
            $q->where('project_id', $projectId);
        })->get();
        
        if ($format === 'zip') {
            return $this->createZipArchive($archives);
        }
        
        return null;
    }
    
    /**
     * إنشاء ملف ZIP
     */
    private function createZipArchive($archives)
    {
        $zip = new \ZipArchive();
        $zipPath = storage_path('app/temp/archive_' . time() . '.zip');
        
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $encryptionService = app(FileEncryptionService::class);
            
            foreach ($archives as $archive) {
                $decryptedContent = $encryptionService->decryptFile($archive->file_path);
                $zip->addFromString($archive->original_name, $decryptedContent);
            }
            
            $zip->close();
        }
        
        return $zipPath;
    }
    
    /**
     * فحص نوع الملف
     */
    private function isInvoice($file)
    {
        $mimeType = $file->getMimeType();
        return in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png']);
    }
}

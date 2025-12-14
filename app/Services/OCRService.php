<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class OCRService
{
    public function extractInvoiceData($imagePath)
    {
        $apiKey = config('services.google.vision_key');
        
        // إذا لم يكن API Key موجود، استخدم OCR البسيط
        if (!$apiKey) {
            return $this->fallbackOCR($imagePath);
        }
        
        try {
            $response = Http::timeout(30)->withHeaders([
                'Content-Type' => 'application/json'
            ])->post('https://vision.googleapis.com/v1/images:annotate?key=' . $apiKey, [
                'requests' => [
                    [
                        'image' => [
                            'content' => base64_encode(Storage::get($imagePath))
                        ],
                        'features' => [
                            ['type' => 'TEXT_DETECTION'],
                            ['type' => 'DOCUMENT_TEXT_DETECTION']
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $text = $response->json()['responses'][0]['textAnnotations'][0]['description'] ?? '';
                return $this->parseInvoiceText($text);
            }
            
            return $this->fallbackOCR($imagePath);
            
        } catch (\Exception $e) {
            \Log::error('OCR Error: ' . $e->getMessage());
            return $this->fallbackOCR($imagePath);
        }
    }
    
    private function parseInvoiceText($text)
    {
        $data = [
            'amount' => null,
            'date' => null,
            'vendor' => null,
            'invoice_number' => null,
            'suggested_category' => null,
            'confidence' => 0
        ];
        
        // استخراج المبلغ - أنماط متعددة
        $amountPatterns = [
            '/(\d+[,.]?\d*)\s*(ريال|ر\.س|SAR|YER|ر س)/ui',
            '/(?:المبلغ|الإجمالي|Total|Amount)[:\s]*(\d+[,.]?\d*)/ui',
            '/(\d{1,3}(?:[,.]\d{3})*(?:\.\d{2})?)\s*(?:ريال|SAR)/ui'
        ];
        
        foreach ($amountPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = preg_replace('/[^0-9.]/', '', $matches[1]);
                if ($amount > 0) {
                    $data['amount'] = $amount;
                    $data['confidence'] += 30;
                    break;
                }
            }
        }
        
        // استخراج التاريخ - أنماط متعددة
        $datePatterns = [
            '/(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/',
            '/(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/',
            '/(\d{1,2}[-\/]\d{1,2}[-\/]\d{2})/',
            '/(?:التاريخ|Date)[:\s]*(\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4})/ui'
        ];
        
        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['date'] = $matches[1];
                $data['confidence'] += 20;
                break;
            }
        }
        
        // استخراج رقم الفاتورة
        $invoicePatterns = [
            '/(?:فاتورة|رقم|Invoice|No\.?|#)[:\s]*(\w+[-]?\w*)/ui',
            '/(?:INV|inv)[:\s-]*(\d+)/i'
        ];
        
        foreach ($invoicePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['invoice_number'] = $matches[1];
                $data['confidence'] += 20;
                break;
            }
        }
        
        // استخراج اسم المورد (أول سطر عادة)
        $lines = explode("\n", $text);
        if (count($lines) > 0) {
            $data['vendor'] = trim($lines[0]);
            $data['confidence'] += 10;
        }
        
        // اقتراح الفئة
        $data['suggested_category'] = $this->suggestCategory($text);
        if ($data['suggested_category']) {
            $data['confidence'] += 20;
        }
        
        return $data;
    }
    
    private function fallbackOCR($imagePath)
    {
        // OCR بسيط باستخدام Tesseract إذا كان متوفر
        $command = "tesseract " . storage_path('app/' . $imagePath) . " stdout -l ara+eng";
        $text = shell_exec($command);
        
        return $this->parseInvoiceText($text ?? '');
    }
    
    public function suggestCategory($text)
    {
        $text = mb_strtolower($text);
        
        $keywords = [
            // طعام وضيافة
            201 => ['طعام', 'فطور', 'غداء', 'عشاء', 'وجبة', 'مطعم', 'restaurant', 'food', 'meal'],
            202 => ['غداء', 'lunch'],
            203 => ['عشاء', 'dinner'],
            204 => ['مشروب', 'قهوة', 'شاي', 'عصير', 'coffee', 'beverage'],
            
            // نقل ومواصلات
            301 => ['وقود', 'بنزين', 'ديزل', 'fuel', 'petrol', 'gas'],
            302 => ['إيجار سيارة', 'car rental', 'تأجير'],
            303 => ['سائق', 'driver', 'أجرة'],
            
            // مواقع وديكور
            401 => ['إيجار موقع', 'location', 'استديو', 'studio'],
            403 => ['ديكور', 'decor', 'decoration'],
            
            // معدات
            501 => ['كاميرا', 'camera', 'تصوير'],
            502 => ['إضاءة', 'lighting', 'light'],
            503 => ['صوت', 'sound', 'audio', 'ميكروفون'],
            
            // أزياء ومكياج
            601 => ['أزياء', 'costume', 'ملابس', 'clothes'],
            603 => ['مكياج', 'makeup', 'تجميل'],
            
            // طاقم
            701 => ['أجور', 'راتب', 'salary', 'wage'],
            
            // إدارية
            801 => ['اتصالات', 'إنترنت', 'internet', 'telecom'],
            802 => ['قرطاسية', 'stationery', 'أوراق'],
            803 => ['طباعة', 'printing', 'print']
        ];
        
        $scores = [];
        
        foreach ($keywords as $code => $words) {
            $score = 0;
            foreach ($words as $word) {
                if (stripos($text, $word) !== false) {
                    $score += strlen($word);
                }
            }
            if ($score > 0) {
                $scores[$code] = $score;
            }
        }
        
        if (empty($scores)) {
            return null;
        }
        
        arsort($scores);
        return array_key_first($scores);
    }
    
    public function detectDuplicateInvoice($invoiceNumber, $amount, $projectId)
    {
        if (!$invoiceNumber) return false;
        
        $existing = \App\Models\Expense::where('project_id', $projectId)
            ->where('invoice_number', $invoiceNumber)
            ->where('amount', $amount)
            ->exists();
            
        return $existing;
    }
}
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EnhancedOCRService
{
    private $confidenceThreshold = 0.7;
    private $supportedLanguages = ['ar', 'en'];
    private $imagePreprocessors = [];
    
    public function __construct()
    {
        $this->initializePreprocessors();
    }
    
    private function initializePreprocessors()
    {
        $this->imagePreprocessors = [
            'contrast_enhancement' => true,
            'noise_reduction' => true,
            'deskew' => true,
            'binarization' => true
        ];
    }

    public function extractInvoiceData($imagePath)
    {
        // معالجة الصورة مسبقاً لتحسين الدقة
        $processedImagePath = $this->preprocessImage($imagePath);
        
        $results = [];
        
        // محاولة متعددة المصادر للحصول على أفضل نتيجة
        $apiKey = config('services.google.vision_key');
        if ($apiKey) {
            $results['google'] = $this->extractWithGoogleVision($processedImagePath, $apiKey);
        }
        
        // استخدام Tesseract كبديل
        $results['tesseract'] = $this->extractWithTesseract($processedImagePath);
        
        // دمج النتائج واختيار الأفضل
        $finalResult = $this->mergeOCRResults($results);
        
        // تنظيف الملف المعالج
        if ($processedImagePath !== $imagePath) {
            Storage::delete($processedImagePath);
        }
        
        return $finalResult;
    }
    
    private function preprocessImage($imagePath)
    {
        $imageContent = Storage::get($imagePath);
        $image = imagecreatefromstring($imageContent);
        
        if (!$image) {
            return $imagePath;
        }
        
        // تحسين التباين
        if ($this->imagePreprocessors['contrast_enhancement']) {
            imagefilter($image, IMG_FILTER_CONTRAST, -20);
        }
        
        // تقليل الضوضاء
        if ($this->imagePreprocessors['noise_reduction']) {
            imagefilter($image, IMG_FILTER_SMOOTH, 1);
        }
        
        // تحويل لرمادي لتحسين دقة OCR
        imagefilter($image, IMG_FILTER_GRAYSCALE);
        
        // حفظ الصورة المعالجة
        $processedPath = 'temp/processed_' . basename($imagePath);
        ob_start();
        imagejpeg($image, null, 95);
        $processedContent = ob_get_contents();
        ob_end_clean();
        
        Storage::put($processedPath, $processedContent);
        imagedestroy($image);
        
        return $processedPath;
    }
    
    private function extractWithGoogleVision($imagePath, $apiKey)
    {
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
                            ['type' => 'DOCUMENT_TEXT_DETECTION', 'maxResults' => 50],
                            ['type' => 'TEXT_DETECTION', 'maxResults' => 50]
                        ],
                        'imageContext' => [
                            'languageHints' => $this->supportedLanguages
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $fullText = $responseData['responses'][0]['fullTextAnnotation']['text'] ?? '';
                $textAnnotations = $responseData['responses'][0]['textAnnotations'] ?? [];
                
                return [
                    'text' => $fullText,
                    'annotations' => $textAnnotations,
                    'confidence' => $this->calculateGoogleConfidence($textAnnotations),
                    'source' => 'google_vision'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Google Vision OCR Error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    private function extractWithTesseract($imagePath)
    {
        try {
            $fullPath = storage_path('app/' . $imagePath);
            
            $command = "tesseract '{$fullPath}' stdout -l ara+eng --psm 6 --oem 3 -c tessedit_char_whitelist=0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzأبتثجحخدذرزسشصضطظعغفقكلمنهويءآإئؤة.,-/: ";
            
            $output = shell_exec($command);
            
            if ($output) {
                return [
                    'text' => $output,
                    'confidence' => 0.8,
                    'source' => 'tesseract'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Tesseract OCR Error: ' . $e->getMessage());
        }
        
        return null;
    }
    
    private function calculateGoogleConfidence($annotations)
    {
        if (empty($annotations)) return 0;
        
        $totalConfidence = 0;
        $count = 0;
        
        foreach ($annotations as $annotation) {
            if (isset($annotation['confidence'])) {
                $totalConfidence += $annotation['confidence'];
                $count++;
            }
        }
        
        return $count > 0 ? $totalConfidence / $count : 0.5;
    }
    
    private function mergeOCRResults($results)
    {
        $bestResult = null;
        $highestConfidence = 0;
        
        foreach ($results as $result) {
            if ($result && $result['confidence'] > $highestConfidence) {
                $bestResult = $result;
                $highestConfidence = $result['confidence'];
            }
        }
        
        if (!$bestResult) {
            return $this->getEmptyResult();
        }
        
        $parsedData = $this->parseInvoiceTextAdvanced($bestResult['text']);
        $parsedData['ocr_confidence'] = $bestResult['confidence'];
        $parsedData['ocr_source'] = $bestResult['source'];
        
        $parsedData = $this->enhanceWithML($parsedData, $bestResult['text']);
        
        return $parsedData;
    }
    
    private function parseInvoiceTextAdvanced($text)
    {
        $data = [
            'amount' => null,
            'date' => null,
            'vendor' => null,
            'invoice_number' => null,
            'suggested_category' => null,
            'confidence' => 0,
            'tax_number' => null,
            'items' => [],
            'total_with_tax' => null,
            'tax_amount' => null
        ];
        
        // استخراج المبلغ - أنماط محسنة
        $amountPatterns = [
            '/(?:المجموع|الإجمالي|Total|Amount|المبلغ)[\:\s]*([\\d,]+\.?\\d*)\s*(?:ريال|ر\.س|SAR|ر س)/ui',
            '/([\\d,]+\.?\\d*)\s*(?:ريال|ر\.س|SAR|ر س)(?:\s*(?:فقط|only))?/ui',
            '/(?:المبلغ المستحق|Amount Due)[\:\s]*([\\d,]+\.?\\d*)/ui',
            '/(?:صافي المبلغ|Net Amount)[\:\s]*([\\d,]+\.?\\d*)/ui',
            '/(\\d{1,3}(?:[,.]\\d{3})*(?:\.\\d{2})?)\\s*(?:ريال|SAR)/ui'
        ];
        
        $amounts = [];
        foreach ($amountPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $amount = preg_replace('/[^0-9.]/', '', $match[1]);
                    if ($amount > 0) {
                        $amounts[] = floatval($amount);
                    }
                }
            }
        }
        
        if (!empty($amounts)) {
            $data['amount'] = max($amounts);
            $data['confidence'] += 30;
            $this->extractTaxInfo($text, $data, $amounts);
        }
        
        // استخراج التاريخ - أنماط محسنة
        $datePatterns = [
            '/(?:التاريخ|تاريخ الإصدار|Date|Issue Date)[\:\s]*(\\d{1,2}[-\/]\\d{1,2}[-\/]\\d{2,4})/ui',
            '/(\\d{4}[-\/]\\d{1,2}[-\/]\\d{1,2})/',
            '/(\\d{1,2}[-\/]\\d{1,2}[-\/]\\d{4})/',
            '/(\\d{1,2}[-\/]\\d{1,2}[-\/]\\d{2})/',
            '/(?:صدرت في|Issued on)[\:\s]*(\\d{1,2}[-\/]\\d{1,2}[-\/]\\d{2,4})/ui',
            '/(\\d{1,2})\\s*[\/\\-]\\s*(\\d{1,2})\\s*[\/\\-]\\s*(\\d{2,4})/',
            '/(\\d{1,2})\\s+(?:يناير|فبراير|مارس|أبريل|مايو|يونيو|يوليو|أغسطس|سبتمبر|أكتوبر|نوفمبر|ديسمبر)\\s+(\\d{4})/ui'
        ];
        
        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $dateStr = $matches[1];
                $data['date'] = $this->normalizeDate($dateStr);
                $data['confidence'] += 20;
                break;
            }
        }
        
        // استخراج رقم الفاتورة - أنماط محسنة
        $invoicePatterns = [
            '/(?:رقم الفاتورة|Invoice No|Invoice Number|فاتورة رقم)[\:\s#]*(\\w+[-\/]?\\w*)/ui',
            '/(?:فاتورة|رقم|Invoice|No\.?|#)[\:\s]*(\\w+[-\/]?\\w*)/ui',
            '/(?:INV|inv)[\:\s-]*(\\d+)/i',
            '/(?:Bill No|Receipt No)[\:\s]*(\\w+)/ui'
        ];
        
        foreach ($invoicePatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['invoice_number'] = trim($matches[1]);
                $data['confidence'] += 20;
                break;
            }
        }
        
        $this->extractTaxNumber($text, $data);
        
        $data['vendor'] = $this->extractVendorName($text);
        if ($data['vendor']) {
            $data['confidence'] += 15;
        }
        
        $data['items'] = $this->extractInvoiceItems($text);
        
        $categoryResult = $this->suggestCategoryAdvanced($text, $data);
        $data['suggested_category'] = $categoryResult['category'];
        $data['category_confidence'] = $categoryResult['confidence'];
        if ($data['suggested_category']) {
            $data['confidence'] += $categoryResult['confidence'] * 20;
        }
        
        return $data;
    }
    
    private function extractTaxInfo($text, &$data, $amounts)
    {
        $taxPatterns = [
            '/(?:ضريبة القيمة المضافة|VAT|Tax)[\:\s]*([\\d,]+\.?\\d*)/ui',
            '/(?:15%|١٥٪)\\s*([\\d,]+\.?\\d*)/ui'
        ];
        
        foreach ($taxPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $taxAmount = preg_replace('/[^0-9.]/', '', $matches[1]);
                $data['tax_amount'] = floatval($taxAmount);
                break;
            }
        }
        
        if ($data['tax_amount'] && $data['amount']) {
            $data['total_with_tax'] = $data['amount'];
            $data['amount'] = $data['amount'] - $data['tax_amount'];
        }
    }
    
    private function extractTaxNumber($text, &$data)
    {
        $taxNumberPatterns = [
            '/(?:الرقم الضريبي|Tax Number|VAT Number)[\:\s]*(\\d+)/ui',
            '/(?:ض\.ب|T\.N)[\:\s]*(\\d+)/ui'
        ];
        
        foreach ($taxNumberPatterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $data['tax_number'] = $matches[1];
                $data['confidence'] += 10;
                break;
            }
        }
    }
    
    private function extractVendorName($text)
    {
        $lines = explode("\n", $text);
        $lines = array_filter(array_map('trim', $lines));
        
        foreach (array_slice($lines, 0, 5) as $line) {
            if (!preg_match('/^[\\d\\s\\-\/]+$/', $line) && strlen($line) > 3) {
                $vendor = preg_replace('/[^\\p{L}\\p{N}\\s\\-\.]/u', '', $line);
                if (strlen($vendor) > 3) {
                    return $vendor;
                }
            }
        }
        
        return null;
    }
    
    private function extractInvoiceItems($text)
    {
        $items = [];
        $lines = explode("\n", $text);
        
        foreach ($lines as $line) {
            if (preg_match('/(.*?)\\s+([\\d,]+\.?\\d*)\\s*(?:ريال|SAR)?/u', $line, $matches)) {
                $description = trim($matches[1]);
                $amount = preg_replace('/[^0-9.]/', '', $matches[2]);
                
                if (strlen($description) > 3 && $amount > 0) {
                    $items[] = [
                        'description' => $description,
                        'amount' => floatval($amount)
                    ];
                }
            }
        }
        
        return $items;
    }
    
    private function normalizeDate($dateStr)
    {
        $dateStr = preg_replace('/[\\s]+/', ' ', trim($dateStr));
        
        try {
            $date = \Carbon\Carbon::createFromFormat('d/m/Y', $dateStr);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateStr);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return $dateStr;
            }
        }
    }
    
    private function enhanceWithML($data, $text)
    {
        if ($data['amount']) {
            $data['amount'] = $this->validateAmount($data['amount'], $text);
        }
        
        if ($data['suggested_category']) {
            $data['suggested_category'] = $this->refineCategorySuggestion(
                $data['suggested_category'], 
                $data['vendor'], 
                $data['amount']
            );
        }
        
        return $data;
    }
    
    private function validateAmount($amount, $text)
    {
        $words = explode(' ', $text);
        $numberWords = array_filter($words, function($word) {
            return preg_match('/\\d/', $word);
        });
        
        $otherNumbers = [];
        foreach ($numberWords as $word) {
            $num = preg_replace('/[^0-9.]/', '', $word);
            if ($num && $num != $amount) {
                $otherNumbers[] = floatval($num);
            }
        }
        
        if (!empty($otherNumbers)) {
            $avgOther = array_sum($otherNumbers) / count($otherNumbers);
            if ($amount > $avgOther * 100) {
                return min($amount, max($otherNumbers));
            }
        }
        
        return $amount;
    }
    
    private function suggestCategoryAdvanced($text, $extractedData = [])
    {
        $text = mb_strtolower($text);
        $vendor = mb_strtolower($extractedData['vendor'] ?? '');
        $amount = $extractedData['amount'] ?? 0;
        
        $keywords = [
            201 => ['طعام', 'فطور', 'غداء', 'عشاء', 'وجبة', 'مطعم', 'restaurant', 'food', 'meal'],
            202 => ['غداء', 'lunch'],
            203 => ['عشاء', 'dinner'],
            204 => ['مشروب', 'قهوة', 'شاي', 'عصير', 'coffee', 'beverage'],
            301 => ['وقود', 'بنزين', 'ديزل', 'fuel', 'petrol', 'gas'],
            302 => ['إيجار سيارة', 'car rental', 'تأجير'],
            303 => ['سائق', 'driver', 'أجرة'],
            401 => ['إيجار موقع', 'location', 'استديو', 'studio'],
            403 => ['ديكور', 'decor', 'decoration'],
            501 => ['كاميرا', 'camera', 'تصوير'],
            502 => ['إضاءة', 'lighting', 'light'],
            503 => ['صوت', 'sound', 'audio', 'ميكروفون'],
            601 => ['أزياء', 'costume', 'ملابس', 'clothes'],
            603 => ['مكياج', 'makeup', 'تجميل'],
            701 => ['أجور', 'راتب', 'salary', 'wage'],
            801 => ['اتصالات', 'إنترنت', 'internet', 'telecom'],
            802 => ['قرطاسية', 'stationery', 'أوراق'],
            803 => ['طباعة', 'printing', 'print']
        ];
        
        $scores = [];
        
        foreach ($keywords as $code => $words) {
            $score = 0;
            foreach ($words as $word) {
                if (stripos($text, $word) !== false) {
                    $score += strlen($word) * 2;
                }
                if (stripos($vendor, $word) !== false) {
                    $score += strlen($word) * 3;
                }
            }
            if ($score > 0) {
                $scores[$code] = $score;
            }
        }
        
        $scores = $this->applyAmountBasedRules($scores, $amount);
        
        if (empty($scores)) {
            return ['category' => null, 'confidence' => 0];
        }
        
        arsort($scores);
        $topCategory = array_key_first($scores);
        $maxScore = max($scores);
        $confidence = min(1, $maxScore / 100);
        
        return [
            'category' => $topCategory,
            'confidence' => $confidence
        ];
    }
    
    private function applyAmountBasedRules($scores, $amount)
    {
        if ($amount > 0) {
            if ($amount > 50000) {
                $scores[500] = ($scores[500] ?? 0) + 20;
                $scores[400] = ($scores[400] ?? 0) + 15;
            } elseif ($amount < 500) {
                $scores[802] = ($scores[802] ?? 0) + 15;
                $scores[204] = ($scores[204] ?? 0) + 10;
            }
        }
        
        return $scores;
    }
    
    private function refineCategorySuggestion($category, $vendor, $amount)
    {
        $knownVendors = [
            'مطعم' => 200,
            'كافيه' => 204,
            'محطة' => 301,
            'ورشة' => 306,
            'صيدلية' => 901
        ];
        
        if ($vendor) {
            foreach ($knownVendors as $vendorType => $suggestedCategory) {
                if (stripos($vendor, $vendorType) !== false) {
                    return $suggestedCategory;
                }
            }
        }
        
        return $category;
    }
    
    private function getEmptyResult()
    {
        return [
            'amount' => null,
            'date' => null,
            'vendor' => null,
            'invoice_number' => null,
            'suggested_category' => null,
            'confidence' => 0,
            'tax_number' => null,
            'items' => [],
            'total_with_tax' => null,
            'tax_amount' => null,
            'ocr_confidence' => 0,
            'ocr_source' => 'none'
        ];
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
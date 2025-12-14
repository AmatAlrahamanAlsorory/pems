<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VideoAnalyticsService
{
    public function analyzeProductionVideo($videoPath)
    {
        try {
            // تحليل الفيديو باستخدام Google Video Intelligence API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.google.video_api_key'),
                'Content-Type' => 'application/json'
            ])->post('https://videointelligence.googleapis.com/v1/videos:annotate', [
                'input_content' => base64_encode(Storage::get($videoPath)),
                'features' => [
                    'LABEL_DETECTION',
                    'SHOT_CHANGE_DETECTION',
                    'PERSON_DETECTION',
                    'FACE_DETECTION'
                ]
            ]);

            if ($response->successful()) {
                return $this->processVideoAnalysis($response->json());
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Video Analysis Error: ' . $e->getMessage());
            return null;
        }
    }

    public function detectProductionElements($videoPath)
    {
        $analysis = $this->analyzeProductionVideo($videoPath);
        
        if (!$analysis) return null;

        return [
            'scenes_detected' => $this->countScenes($analysis),
            'actors_detected' => $this->countActors($analysis),
            'equipment_detected' => $this->detectEquipment($analysis),
            'locations_identified' => $this->identifyLocations($analysis),
            'production_quality' => $this->assessQuality($analysis),
            'estimated_costs' => $this->estimateCosts($analysis)
        ];
    }

    public function generateProductionReport($videoPath, $projectId)
    {
        $elements = $this->detectProductionElements($videoPath);
        
        if (!$elements) return null;

        $report = [
            'project_id' => $projectId,
            'video_path' => $videoPath,
            'analysis_date' => now(),
            'scenes_count' => $elements['scenes_detected'],
            'actors_count' => $elements['actors_detected'],
            'equipment_list' => $elements['equipment_detected'],
            'locations' => $elements['locations_identified'],
            'quality_score' => $elements['production_quality'],
            'cost_breakdown' => $elements['estimated_costs'],
            'recommendations' => $this->generateRecommendations($elements)
        ];

        // حفظ التقرير
        \App\Models\VideoAnalysisReport::create($report);

        return $report;
    }

    public function trackActorScreenTime($videoPath)
    {
        $analysis = $this->analyzeProductionVideo($videoPath);
        
        $screenTime = [];
        
        if (isset($analysis['person_detections'])) {
            foreach ($analysis['person_detections'] as $detection) {
                $personId = $detection['person_id'] ?? 'unknown';
                $duration = $detection['end_time'] - $detection['start_time'];
                
                if (!isset($screenTime[$personId])) {
                    $screenTime[$personId] = 0;
                }
                
                $screenTime[$personId] += $duration;
            }
        }

        return $screenTime;
    }

    public function detectProductionIssues($videoPath)
    {
        $analysis = $this->analyzeProductionVideo($videoPath);
        $issues = [];

        // فحص جودة الصوت
        if (isset($analysis['audio_quality']) && $analysis['audio_quality'] < 0.7) {
            $issues[] = [
                'type' => 'audio_quality',
                'severity' => 'medium',
                'description' => 'جودة الصوت منخفضة',
                'recommendation' => 'تحسين معدات الصوت'
            ];
        }

        // فحص جودة الإضاءة
        if (isset($analysis['lighting_quality']) && $analysis['lighting_quality'] < 0.6) {
            $issues[] = [
                'type' => 'lighting',
                'severity' => 'high',
                'description' => 'إضاءة غير كافية',
                'recommendation' => 'إضافة معدات إضاءة'
            ];
        }

        // فحص استقرار الكاميرا
        if (isset($analysis['camera_stability']) && $analysis['camera_stability'] < 0.8) {
            $issues[] = [
                'type' => 'camera_stability',
                'severity' => 'low',
                'description' => 'اهتزاز في الكاميرا',
                'recommendation' => 'استخدام حامل ثلاثي'
            ];
        }

        return $issues;
    }

    private function processVideoAnalysis($apiResponse)
    {
        return [
            'labels' => $apiResponse['annotation_results'][0]['segment_label_annotations'] ?? [],
            'shots' => $apiResponse['annotation_results'][0]['shot_annotations'] ?? [],
            'persons' => $apiResponse['annotation_results'][0]['person_detections'] ?? [],
            'faces' => $apiResponse['annotation_results'][0]['face_annotations'] ?? []
        ];
    }

    private function countScenes($analysis)
    {
        return count($analysis['shots'] ?? []);
    }

    private function countActors($analysis)
    {
        $uniquePersons = [];
        
        foreach ($analysis['persons'] ?? [] as $person) {
            $personId = $person['person_id'] ?? uniqid();
            $uniquePersons[$personId] = true;
        }

        return count($uniquePersons);
    }

    private function detectEquipment($analysis)
    {
        $equipment = [];
        $equipmentKeywords = ['camera', 'microphone', 'light', 'tripod', 'monitor'];

        foreach ($analysis['labels'] ?? [] as $label) {
            $description = strtolower($label['entity']['description'] ?? '');
            
            foreach ($equipmentKeywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    $equipment[] = $label['entity']['description'];
                    break;
                }
            }
        }

        return array_unique($equipment);
    }

    private function identifyLocations($analysis)
    {
        $locations = [];
        $locationKeywords = ['indoor', 'outdoor', 'studio', 'office', 'street', 'building'];

        foreach ($analysis['labels'] ?? [] as $label) {
            $description = strtolower($label['entity']['description'] ?? '');
            
            foreach ($locationKeywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    $locations[] = $label['entity']['description'];
                    break;
                }
            }
        }

        return array_unique($locations);
    }

    private function assessQuality($analysis)
    {
        $qualityScore = 0.8; // نقطة بداية

        // تقييم بناءً على عدد المشاهد
        $scenesCount = $this->countScenes($analysis);
        if ($scenesCount > 10) $qualityScore += 0.1;
        if ($scenesCount < 3) $qualityScore -= 0.2;

        // تقييم بناءً على وضوح الوجوه
        $facesCount = count($analysis['faces'] ?? []);
        if ($facesCount > 0) $qualityScore += 0.1;

        return min(1.0, max(0.0, $qualityScore));
    }

    private function estimateCosts($analysis)
    {
        $baseCost = 10000; // تكلفة أساسية
        
        $actorsCount = $this->countActors($analysis);
        $scenesCount = $this->countScenes($analysis);
        $equipmentCount = count($this->detectEquipment($analysis));

        return [
            'actors_cost' => $actorsCount * 2000,
            'equipment_cost' => $equipmentCount * 500,
            'location_cost' => $scenesCount * 1000,
            'total_estimated' => $baseCost + ($actorsCount * 2000) + ($equipmentCount * 500) + ($scenesCount * 1000)
        ];
    }

    private function generateRecommendations($elements)
    {
        $recommendations = [];

        if ($elements['production_quality'] < 0.7) {
            $recommendations[] = 'تحسين جودة الإنتاج العامة';
        }

        if (count($elements['equipment_detected']) < 3) {
            $recommendations[] = 'إضافة معدات إنتاج إضافية';
        }

        if ($elements['actors_detected'] > 10) {
            $recommendations[] = 'مراجعة تكاليف الممثلين';
        }

        return $recommendations;
    }
}
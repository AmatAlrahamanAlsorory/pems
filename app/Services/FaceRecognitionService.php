<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FaceRecognitionService
{
    public function registerEmployee($employeeId, $photoPath)
    {
        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => config('services.azure.face_api_key'),
                'Content-Type' => 'application/octet-stream'
            ])->post(config('services.azure.face_endpoint') . '/face/v1.0/persongroups/employees/persons', [
                'name' => 'Employee_' . $employeeId,
                'userData' => json_encode(['employee_id' => $employeeId])
            ]);

            if ($response->successful()) {
                $personId = $response->json()['personId'];
                
                // إضافة صورة الوجه
                $this->addFaceToEmployee($personId, $photoPath);
                
                // حفظ في قاعدة البيانات
                \App\Models\EmployeeFace::create([
                    'employee_id' => $employeeId,
                    'person_id' => $personId,
                    'photo_path' => $photoPath,
                    'registered_at' => now()
                ]);

                return ['success' => true, 'person_id' => $personId];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            \Log::error('Face Registration Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function markAttendance($photoPath, $locationId)
    {
        try {
            // اكتشاف الوجه في الصورة
            $faceResponse = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => config('services.azure.face_api_key'),
                'Content-Type' => 'application/octet-stream'
            ])->withBody(Storage::get($photoPath), 'application/octet-stream')
            ->post(config('services.azure.face_endpoint') . '/face/v1.0/detect');

            if (!$faceResponse->successful() || empty($faceResponse->json())) {
                return ['success' => false, 'error' => 'لم يتم اكتشاف وجه في الصورة'];
            }

            $faceId = $faceResponse->json()[0]['faceId'];

            // التعرف على الوجه
            $identifyResponse = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => config('services.azure.face_api_key'),
                'Content-Type' => 'application/json'
            ])->post(config('services.azure.face_endpoint') . '/face/v1.0/identify', [
                'personGroupId' => 'employees',
                'faceIds' => [$faceId],
                'maxNumOfCandidatesReturned' => 1,
                'confidenceThreshold' => 0.7
            ]);

            if ($identifyResponse->successful() && !empty($identifyResponse->json()[0]['candidates'])) {
                $personId = $identifyResponse->json()[0]['candidates'][0]['personId'];
                $confidence = $identifyResponse->json()[0]['candidates'][0]['confidence'];

                // البحث عن الموظف
                $employeeFace = \App\Models\EmployeeFace::where('person_id', $personId)->first();
                
                if ($employeeFace) {
                    // تسجيل الحضور
                    $attendance = \App\Models\Attendance::create([
                        'employee_id' => $employeeFace->employee_id,
                        'location_id' => $locationId,
                        'check_in_time' => now(),
                        'photo_path' => $photoPath,
                        'confidence_score' => $confidence,
                        'recognition_method' => 'face'
                    ]);

                    return [
                        'success' => true,
                        'employee_id' => $employeeFace->employee_id,
                        'employee_name' => $employeeFace->employee->name ?? 'غير معروف',
                        'confidence' => $confidence,
                        'attendance_id' => $attendance->id
                    ];
                }
            }

            return ['success' => false, 'error' => 'لم يتم التعرف على الوجه'];
        } catch (\Exception $e) {
            \Log::error('Face Recognition Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function markCheckOut($employeeId, $locationId)
    {
        $attendance = \App\Models\Attendance::where('employee_id', $employeeId)
            ->where('location_id', $locationId)
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        if ($attendance) {
            $attendance->update([
                'check_out_time' => now(),
                'total_hours' => now()->diffInHours($attendance->check_in_time)
            ]);

            return ['success' => true, 'total_hours' => $attendance->total_hours];
        }

        return ['success' => false, 'error' => 'لم يتم العثور على سجل حضور'];
    }

    public function getAttendanceReport($locationId, $date = null)
    {
        $query = \App\Models\Attendance::with('employee')
            ->where('location_id', $locationId);

        if ($date) {
            $query->whereDate('check_in_time', $date);
        } else {
            $query->whereDate('check_in_time', today());
        }

        $attendances = $query->get();

        return [
            'total_employees' => $attendances->count(),
            'present_employees' => $attendances->whereNotNull('check_in_time')->count(),
            'absent_employees' => $this->getAbsentEmployees($locationId, $date),
            'average_hours' => $attendances->avg('total_hours'),
            'attendances' => $attendances->map(function($attendance) {
                return [
                    'employee_name' => $attendance->employee->name ?? 'غير معروف',
                    'check_in' => $attendance->check_in_time,
                    'check_out' => $attendance->check_out_time,
                    'total_hours' => $attendance->total_hours,
                    'confidence' => $attendance->confidence_score
                ];
            })
        ];
    }

    public function trainPersonGroup()
    {
        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => config('services.azure.face_api_key')
            ])->post(config('services.azure.face_endpoint') . '/face/v1.0/persongroups/employees/train');

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Person Group Training Error: ' . $e->getMessage());
            return false;
        }
    }

    public function createPersonGroup()
    {
        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => config('services.azure.face_api_key'),
                'Content-Type' => 'application/json'
            ])->put(config('services.azure.face_endpoint') . '/face/v1.0/persongroups/employees', [
                'name' => 'PEMS Employees',
                'userData' => 'Employee faces for attendance tracking'
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Person Group Creation Error: ' . $e->getMessage());
            return false;
        }
    }

    private function addFaceToEmployee($personId, $photoPath)
    {
        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => config('services.azure.face_api_key'),
                'Content-Type' => 'application/octet-stream'
            ])->withBody(Storage::get($photoPath), 'application/octet-stream')
            ->post(config('services.azure.face_endpoint') . "/face/v1.0/persongroups/employees/persons/{$personId}/persistedFaces");

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Add Face Error: ' . $e->getMessage());
            return false;
        }
    }

    private function getAbsentEmployees($locationId, $date)
    {
        $date = $date ?? today();
        
        $presentEmployees = \App\Models\Attendance::where('location_id', $locationId)
            ->whereDate('check_in_time', $date)
            ->pluck('employee_id');

        $totalEmployees = \App\Models\Person::where('status', 'active')->count();
        
        return $totalEmployees - $presentEmployees->count();
    }

    public function generateAttendancePayroll($locationId, $startDate, $endDate)
    {
        $attendances = \App\Models\Attendance::with('employee')
            ->where('location_id', $locationId)
            ->whereBetween('check_in_time', [$startDate, $endDate])
            ->get()
            ->groupBy('employee_id');

        $payroll = [];

        foreach ($attendances as $employeeId => $employeeAttendances) {
            $totalHours = $employeeAttendances->sum('total_hours');
            $totalDays = $employeeAttendances->count();
            $employee = $employeeAttendances->first()->employee;

            $payroll[] = [
                'employee_id' => $employeeId,
                'employee_name' => $employee->name ?? 'غير معروف',
                'total_days' => $totalDays,
                'total_hours' => $totalHours,
                'hourly_rate' => $employee->hourly_rate ?? 50,
                'total_salary' => $totalHours * ($employee->hourly_rate ?? 50),
                'overtime_hours' => max(0, $totalHours - ($totalDays * 8)),
                'overtime_pay' => max(0, $totalHours - ($totalDays * 8)) * ($employee->hourly_rate ?? 50) * 1.5
            ];
        }

        return $payroll;
    }
}
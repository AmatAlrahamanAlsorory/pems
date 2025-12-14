<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalIntegrationService
{
    // تكامل SAP
    public function syncWithSAP($expenseData)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.sap.token'),
                'Content-Type' => 'application/json'
            ])->post(config('services.sap.endpoint') . '/expenses', [
                'document_type' => 'ZE',
                'company_code' => config('services.sap.company_code'),
                'posting_date' => $expenseData['expense_date'],
                'document_date' => $expenseData['expense_date'],
                'reference' => $expenseData['invoice_number'],
                'header_text' => $expenseData['description'],
                'items' => [
                    [
                        'gl_account' => $this->mapCategoryToGLAccount($expenseData['category_id']),
                        'amount' => $expenseData['amount'],
                        'cost_center' => $expenseData['project_id'],
                        'text' => $expenseData['description']
                    ]
                ]
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('SAP Integration Error: ' . $e->getMessage());
            return null;
        }
    }

    // تكامل Oracle
    public function syncWithOracle($projectData)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(config('services.oracle.username') . ':' . config('services.oracle.password')),
                'Content-Type' => 'application/json'
            ])->post(config('services.oracle.endpoint') . '/projects', [
                'project_number' => $projectData['id'],
                'project_name' => $projectData['name'],
                'project_type' => $projectData['type'],
                'start_date' => $projectData['start_date'],
                'end_date' => $projectData['end_date'],
                'budget_amount' => $projectData['total_budget']
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Oracle Integration Error: ' . $e->getMessage());
            return false;
        }
    }

    // تكامل البنوك
    public function processPayment($paymentData)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.bank.api_key'),
                'Content-Type' => 'application/json'
            ])->post(config('services.bank.endpoint') . '/payments', [
                'amount' => $paymentData['amount'],
                'currency' => 'SAR',
                'beneficiary_account' => $paymentData['account_number'],
                'beneficiary_name' => $paymentData['beneficiary_name'],
                'reference' => $paymentData['reference'],
                'purpose_code' => 'EXPENSE_PAYMENT'
            ]);

            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'transaction_id' => $response->json()['transaction_id'],
                    'reference' => $response->json()['reference']
                ];
            }

            return ['status' => 'failed', 'error' => $response->body()];
        } catch (\Exception $e) {
            Log::error('Bank Integration Error: ' . $e->getMessage());
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    // تكامل الموارد البشرية
    public function syncEmployeeData($employeeId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hr.token')
            ])->get(config('services.hr.endpoint') . '/employees/' . $employeeId);

            if ($response->successful()) {
                $employeeData = $response->json();
                
                // تحديث بيانات الموظف في النظام
                \App\Models\Person::updateOrCreate(
                    ['employee_id' => $employeeId],
                    [
                        'name' => $employeeData['full_name'],
                        'position' => $employeeData['position'],
                        'department' => $employeeData['department'],
                        'salary' => $employeeData['salary'],
                        'status' => $employeeData['status']
                    ]
                );

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('HR Integration Error: ' . $e->getMessage());
            return false;
        }
    }

    private function mapCategoryToGLAccount($categoryId)
    {
        $mapping = [
            100 => '6100001', // مصروفات الممثلين
            200 => '6200001', // مصروفات الطعام
            300 => '6300001', // مصروفات النقل
            400 => '6400001', // مصروفات المواقع
            500 => '6500001', // مصروفات المعدات
            600 => '6600001', // مصروفات الأزياء
            700 => '6700001', // مصروفات الطاقم
            800 => '6800001', // مصروفات إدارية
            900 => '6900001'  // مصروفات طوارئ
        ];

        return $mapping[$categoryId] ?? '6000001';
    }
}
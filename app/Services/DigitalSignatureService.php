<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class DigitalSignatureService
{
    private $privateKeyPath;
    private $publicKeyPath;

    public function __construct()
    {
        $this->privateKeyPath = storage_path('keys/private.pem');
        $this->publicKeyPath = storage_path('keys/public.pem');
        $this->ensureKeysExist();
    }

    public function signDocument($documentPath, $userId)
    {
        try {
            $documentContent = Storage::get($documentPath);
            $hash = hash('sha256', $documentContent);
            
            $privateKey = openssl_pkey_get_private(file_get_contents($this->privateKeyPath));
            openssl_sign($hash, $signature, $privateKey, OPENSSL_ALGO_SHA256);
            
            $signatureData = [
                'signature' => base64_encode($signature),
                'hash' => $hash,
                'user_id' => $userId,
                'timestamp' => now()->toISOString(),
                'algorithm' => 'SHA256withRSA'
            ];

            // حفظ التوقيع في قاعدة البيانات
            \App\Models\DigitalSignature::create([
                'document_path' => $documentPath,
                'user_id' => $userId,
                'signature_data' => json_encode($signatureData),
                'hash' => $hash,
                'signed_at' => now()
            ]);

            return $signatureData;
        } catch (\Exception $e) {
            \Log::error('Digital Signature Error: ' . $e->getMessage());
            return null;
        }
    }

    public function verifySignature($documentPath, $signatureData)
    {
        try {
            $documentContent = Storage::get($documentPath);
            $currentHash = hash('sha256', $documentContent);
            
            // التحقق من سلامة المستند
            if ($currentHash !== $signatureData['hash']) {
                return ['valid' => false, 'reason' => 'Document has been modified'];
            }

            $publicKey = openssl_pkey_get_public(file_get_contents($this->publicKeyPath));
            $signature = base64_decode($signatureData['signature']);
            
            $isValid = openssl_verify($signatureData['hash'], $signature, $publicKey, OPENSSL_ALGO_SHA256);
            
            return [
                'valid' => $isValid === 1,
                'signer' => \App\Models\User::find($signatureData['user_id'])->name,
                'signed_at' => $signatureData['timestamp'],
                'algorithm' => $signatureData['algorithm']
            ];
        } catch (\Exception $e) {
            \Log::error('Signature Verification Error: ' . $e->getMessage());
            return ['valid' => false, 'reason' => 'Verification failed'];
        }
    }

    public function createSignaturePad()
    {
        return view('components.signature-pad');
    }

    public function processSignaturePad($signatureData, $documentId, $userId)
    {
        try {
            // تحويل التوقيع من base64 إلى صورة
            $signatureImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData));
            $signaturePath = 'signatures/' . $documentId . '_' . $userId . '_' . time() . '.png';
            
            Storage::put($signaturePath, $signatureImage);

            // إنشاء توقيع رقمي للصورة
            $hash = hash('sha256', $signatureImage);
            
            \App\Models\DigitalSignature::create([
                'document_id' => $documentId,
                'user_id' => $userId,
                'signature_path' => $signaturePath,
                'signature_type' => 'pad',
                'hash' => $hash,
                'signed_at' => now()
            ]);

            return ['success' => true, 'signature_path' => $signaturePath];
        } catch (\Exception $e) {
            \Log::error('Signature Pad Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function ensureKeysExist()
    {
        if (!file_exists($this->privateKeyPath) || !file_exists($this->publicKeyPath)) {
            $this->generateKeyPair();
        }
    }

    private function generateKeyPair()
    {
        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        $publicKey = openssl_pkey_get_details($res)['key'];

        if (!is_dir(dirname($this->privateKeyPath))) {
            mkdir(dirname($this->privateKeyPath), 0755, true);
        }

        file_put_contents($this->privateKeyPath, $privateKey);
        file_put_contents($this->publicKeyPath, $publicKey);
    }
}
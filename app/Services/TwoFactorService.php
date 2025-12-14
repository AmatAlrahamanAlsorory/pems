<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class TwoFactorService
{
    public function generateCode(User $user)
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Cache::put("2fa_code_{$user->id}", $code, now()->addMinutes(5));
        
        return $code;
    }
    
    public function sendCode(User $user)
    {
        $code = $this->generateCode($user);
        
        // إرسال عبر SMS (يحتاج خدمة SMS)
        if ($user->phone) {
            $this->sendSMS($user->phone, $code);
        }
        
        // إرسال عبر البريد الإلكتروني
        Mail::raw("رمز التحقق الخاص بك: {$code}\nصالح لمدة 5 دقائق", function($message) use ($user) {
            $message->to($user->email)
                   ->subject('رمز التحقق - نظام PEMS');
        });
        
        return true;
    }
    
    public function verifyCode(User $user, $code)
    {
        $storedCode = Cache::get("2fa_code_{$user->id}");
        
        if (!$storedCode || $storedCode !== $code) {
            return false;
        }
        
        Cache::forget("2fa_code_{$user->id}");
        Cache::put("2fa_verified_{$user->id}", true, now()->addHours(24));
        
        return true;
    }
    
    public function isVerified(User $user)
    {
        return Cache::has("2fa_verified_{$user->id}");
    }
    
    public function requiresTwoFactor(User $user)
    {
        return in_array($user->role, ['financial_manager', 'admin_accountant']);
    }
    
    private function sendSMS($phone, $code)
    {
        // تكامل مع خدمة SMS (مثل Twilio أو خدمة محلية)
        // هذا مثال بسيط
        try {
            // $client = new TwilioClient($sid, $token);
            // $client->messages->create($phone, ['from' => $from, 'body' => "رمز التحقق: {$code}"]);
        } catch (\Exception $e) {
            \Log::error('SMS sending failed: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('auth.two-factor');
    }
    
    public function send(Request $request)
    {
        $twoFactorService = app(TwoFactorService::class);
        $twoFactorService->sendCode(auth()->user());
        
        return back()->with('success', 'تم إرسال رمز التحقق');
    }
    
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);
        
        $twoFactorService = app(TwoFactorService::class);
        
        if ($twoFactorService->verifyCode(auth()->user(), $request->code)) {
            return redirect()->intended('/dashboard');
        }
        
        return back()->withErrors(['code' => 'رمز التحقق غير صحيح']);
    }
}
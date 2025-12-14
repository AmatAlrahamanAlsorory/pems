<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\TwoFactorService;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }
        
        $twoFactorService = app(TwoFactorService::class);
        
        if ($twoFactorService->requiresTwoFactor($user) && !$twoFactorService->isVerified($user)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Two factor authentication required'], 403);
            }
            
            return redirect()->route('2fa.verify');
        }
        
        return $next($request);
    }
}
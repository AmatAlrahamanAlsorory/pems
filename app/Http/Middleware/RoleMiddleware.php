<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userRole = auth()->user()->role;
        
        // التحقق من الأدوار المسموحة
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
    }
}
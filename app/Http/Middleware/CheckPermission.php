<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\PermissionHelper;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!PermissionHelper::hasPermission(auth()->user(), $permission)) {
            abort(403, 'غير مصرح لك بتنفيذ هذا الإجراء');
        }

        return $next($request);
    }
}
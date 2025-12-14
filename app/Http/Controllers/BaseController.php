<?php

namespace App\Http\Controllers;

use App\Helpers\PermissionHelper;

abstract class BaseController extends Controller
{
    protected function authorize(string $permission, string $message = 'غير مصرح لك بتنفيذ هذا الإجراء'): void
    {
        if (!PermissionHelper::hasPermission(auth()->user(), $permission)) {
            abort(403, $message);
        }
    }

    protected function authorizeRole(array $roles, string $message = 'غير مصرح لك بالوصول إلى هذه الصفحة'): void
    {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
            abort(403, $message);
        }
    }
}

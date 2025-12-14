<?php

namespace App\Helpers;

class PermissionHelper
{
    // تعريف الصلاحيات لكل دور
    private static $rolePermissions = [
        'financial_manager' => [
            // كل الصلاحيات - المدير المالي العام
            'create_project', 'edit_project', 'delete_project', 'view_project',
            'create_custody', 'approve_custody', 'view_custody',
            'create_expense', 'approve_expense', 'view_expense',
            'view_reports', 'export_reports', 'view_exceptions',
            'manage_users', 'manage_locations', 'manage_people',
            'view_dashboard', 'system_settings'
        ],
        'admin_accountant' => [
            // محاسب الإدارة - مراقبة وموافقات
            'create_project', 'edit_project', 'view_project',
            'create_custody', 'approve_custody', 'view_custody',
            'create_expense', 'approve_expense', 'view_expense',
            'view_reports', 'export_reports', 'view_exceptions',
            'manage_locations', 'manage_people', 'view_dashboard'
        ],
        'production_manager' => [
            // مدير الإنتاج - إدارة الموقع والموافقات المحدودة
            'create_project', 'view_project',
            'create_custody', 'approve_custody', 'view_custody',
            'create_expense', 'approve_expense', 'view_expense',
            'view_reports', 'manage_locations', 'manage_people', 'view_dashboard'
        ],
        'field_accountant' => [
            // المحاسب الميداني - تسجيل وعرض
            'view_project', 'create_custody', 'view_custody',
            'create_expense', 'view_expense', 'view_dashboard'
        ],
        'financial_assistant' => [
            // المساعد المالي - تسجيل فقط
            'view_project', 'view_custody',
            'create_expense', 'view_expense', 'view_dashboard'
        ]
    ];
    
    public static function hasPermission($user, $permission)
    {
        if (!$user || !$user->role) {
            return false;
        }
        
        $rolePermissions = self::$rolePermissions[$user->role] ?? [];
        return in_array($permission, $rolePermissions);
    }
    
    public static function canCreateProject($user)
    {
        return self::hasPermission($user, 'create_project');
    }
    
    public static function canViewProject($user)
    {
        return self::hasPermission($user, 'view_project');
    }
    
    public static function canEditProject($user)
    {
        return self::hasPermission($user, 'edit_project');
    }
    
    public static function canDeleteProject($user)
    {
        return self::hasPermission($user, 'delete_project');
    }
    
    public static function canCreateCustody($user)
    {
        return self::hasPermission($user, 'create_custody');
    }
    
    public static function canApproveCustody($user)
    {
        return self::hasPermission($user, 'approve_custody');
    }
    
    public static function canCreateExpense($user)
    {
        return self::hasPermission($user, 'create_expense');
    }
    
    public static function canApproveExpense($user)
    {
        return self::hasPermission($user, 'approve_expense');
    }
    
    public static function canViewReports($user)
    {
        return self::hasPermission($user, 'view_reports');
    }
    
    public static function canViewExceptions($user)
    {
        return self::hasPermission($user, 'view_exceptions');
    }
    
    public static function canManageUsers($user)
    {
        return self::hasPermission($user, 'manage_users');
    }
    
    public static function canManageLocations($user)
    {
        return self::hasPermission($user, 'manage_locations');
    }
    
    public static function canManagePeople($user)
    {
        return self::hasPermission($user, 'manage_people');
    }
    
    public static function canExportReports($user)
    {
        return self::hasPermission($user, 'export_reports');
    }
    
    public static function canAccessSystemSettings($user)
    {
        return self::hasPermission($user, 'system_settings');
    }
    
    public static function getRoleName($role)
    {
        $roles = [
            'financial_manager' => 'المدير المالي العام',
            'admin_accountant' => 'محاسب الإدارة',
            'production_manager' => 'مدير إنتاج الموقع',
            'field_accountant' => 'المحاسب الميداني',
            'financial_assistant' => 'مساعد مالي'
        ];
        
        return $roles[$role] ?? 'غير محدد';
    }
}
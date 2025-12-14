<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'location'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // صلاحيات الأدوار - موحدة مع PermissionHelper
    public function hasPermission(string $permission): bool
    {
        return \App\Helpers\PermissionHelper::hasPermission($this, $permission);
    }

    public function canApproveExpenses(): bool
    {
        return $this->hasPermission('approve_expense');
    }

    public function canManageProjects(): bool
    {
        return $this->hasPermission('create_project');
    }

    public function canCreateCustodies(): bool
    {
        return $this->hasPermission('create_custody');
    }

    public function canApproveCustodies(): bool
    {
        return $this->hasPermission('approve_custody');
    }

    public function canViewAllProjects(): bool
    {
        return $this->hasPermission('view_project');
    }

    public function isFieldUser(): bool
    {
        return in_array($this->role, ['production_manager', 'field_accountant', 'financial_assistant']);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->orderBy('created_at', 'desc');
    }

    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'financial_manager' => 'المدير المالي العام',
            'admin_accountant' => 'محاسب الإدارة',
            'production_manager' => 'مدير إنتاج الموقع',
            'field_accountant' => 'المحاسب الميداني',
            'financial_assistant' => 'مساعد مالي',
            default => 'غير محدد'
        };
    }
    
    public function hasRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return false;
    }
}
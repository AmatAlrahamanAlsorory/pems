<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public function log($action, $model = null, $oldValues = null, $newValues = null)
    {
        DB::table('audit_logs')->insert([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now()
        ]);
    }
    
    public function logLogin($user)
    {
        $this->log('login', $user, null, [
            'login_time' => now(),
            'ip' => Request::ip()
        ]);
    }
    
    public function logLogout($user)
    {
        $this->log('logout', $user, null, [
            'logout_time' => now()
        ]);
    }
    
    public function logCreate($model)
    {
        $this->log('create', $model, null, $model->toArray());
    }
    
    public function logUpdate($model, $oldValues)
    {
        $this->log('update', $model, $oldValues, $model->toArray());
    }
    
    public function logDelete($model)
    {
        $this->log('delete', $model, $model->toArray(), null);
    }
    
    public function logApproval($model, $status)
    {
        $this->log('approval', $model, null, [
            'status' => $status,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
    }
    
    public function getAuditLogs($filters = [])
    {
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as user_name')
            ->orderBy('audit_logs.created_at', 'desc');
            
        if (isset($filters['user_id'])) {
            $query->where('audit_logs.user_id', $filters['user_id']);
        }
        
        if (isset($filters['action'])) {
            $query->where('audit_logs.action', $filters['action']);
        }
        
        if (isset($filters['model_type'])) {
            $query->where('audit_logs.model_type', $filters['model_type']);
        }
        
        if (isset($filters['date_from'])) {
            $query->whereDate('audit_logs.created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('audit_logs.created_at', '<=', $filters['date_to']);
        }
        
        return $query->paginate(50);
    }
}
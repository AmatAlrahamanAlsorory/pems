<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Helpers\PermissionHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function financial_manager_has_all_permissions()
    {
        $user = User::factory()->create(['role' => 'financial_manager']);
        
        $this->assertTrue(PermissionHelper::hasPermission($user, 'create_project'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'edit_project'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'delete_project'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'approve_custody'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'approve_expense'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'manage_users'));
    }

    /** @test */
    public function admin_accountant_cannot_create_projects()
    {
        $user = User::factory()->create(['role' => 'admin_accountant']);
        
        $this->assertFalse(PermissionHelper::hasPermission($user, 'create_project'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'view_project'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'approve_custody'));
    }

    /** @test */
    public function production_manager_can_approve_expenses()
    {
        $user = User::factory()->create(['role' => 'production_manager']);
        
        $this->assertTrue(PermissionHelper::hasPermission($user, 'approve_expense'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'create_custody'));
        $this->assertFalse(PermissionHelper::hasPermission($user, 'approve_custody'));
    }

    /** @test */
    public function field_accountant_has_limited_permissions()
    {
        $user = User::factory()->create(['role' => 'field_accountant']);
        
        $this->assertTrue(PermissionHelper::hasPermission($user, 'create_expense'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'view_expense'));
        $this->assertFalse(PermissionHelper::hasPermission($user, 'approve_expense'));
        $this->assertFalse(PermissionHelper::hasPermission($user, 'create_project'));
    }

    /** @test */
    public function financial_assistant_has_minimal_permissions()
    {
        $user = User::factory()->create(['role' => 'financial_assistant']);
        
        $this->assertTrue(PermissionHelper::hasPermission($user, 'create_expense'));
        $this->assertTrue(PermissionHelper::hasPermission($user, 'view_expense'));
        $this->assertFalse(PermissionHelper::hasPermission($user, 'create_custody'));
        $this->assertFalse(PermissionHelper::hasPermission($user, 'approve_expense'));
    }

    /** @test */
    public function unauthorized_user_cannot_access_protected_routes()
    {
        $user = User::factory()->create(['role' => 'financial_assistant']);
        
        $response = $this->actingAs($user)->get(route('projects.create'));
        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_user_can_access_protected_routes()
    {
        $user = User::factory()->create(['role' => 'financial_manager']);
        
        $response = $this->actingAs($user)->get(route('projects.create'));
        $response->assertStatus(200);
    }
}

<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacTest extends TestCase
{
    public function test_all_prd_roles_are_seeded(): void
    {
        $expectedRoles = [
            'super_admin',
            'director',
            'sales_manager',
            'sales',
            'presales',
            'marketing_lead',
            'content_creator',
            'hrd_manager',
            'hrd_staff',
            'project_manager',
            'tech_lead',
            'developer',
            'qa_engineer',
            'finance_manager',
            'finance_ar_specialist',
            'legal_counsel',
            'client',
        ];

        foreach ($expectedRoles as $roleName) {
            $this->assertDatabaseHas('roles', ['name' => $roleName]);
        }
    }

    public function test_role_and_permission_checks(): void
    {
        $directorRole = Role::where('name', 'director')->firstOrFail();
        $this->assertGreaterThan(0, $directorRole->permissions()->count());

        $user = User::create([
            'name' => 'Ahmad Direktur',
            'email' => 'director.test@example.com',
            'role' => 'director',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole($directorRole);

        $this->assertTrue($user->hasRole('director'));
        $this->assertTrue($user->hasPermissionTo('crm.discount.approve'));
    }

    public function test_super_admin_can_impersonate_user_with_audit_logging(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => 'super_admin',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $targetUser = User::create([
            'name' => 'Citra Target',
            'email' => 'citra.target@example.com',
            'role' => 'sales',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $adminToken = $admin->createToken('admin')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->postJson("/api/users/{$targetUser->id}/impersonate");

        $response->assertStatus(200)
            ->assertJsonPath('data.impersonated_user.email', 'citra.target@example.com')
            ->assertJsonStructure(['data' => ['impersonated_user', 'token']]);

        // Verify audit log exists
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'security.impersonate',
            'user_id' => (string) $admin->id,
            'entity_id' => (string) $targetUser->id,
        ]);
    }

    public function test_non_admin_cannot_impersonate(): void
    {
        $salesUser = User::create([
            'name' => 'Sales Citra',
            'email' => 'sales.normal@example.com',
            'role' => 'sales',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $targetUser = User::create([
            'name' => 'Target User',
            'email' => 'target@example.com',
            'role' => 'developer',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $token = $salesUser->createToken('sales')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/users/{$targetUser->id}/impersonate");

        $response->assertStatus(403);
    }
}


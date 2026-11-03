<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    /**
     * E2E-08: User Sales accesses sensitive salary records -> 403 Forbidden + Audit Log Recorded
     */
    public function test_e2e_08_sales_accessing_salary_is_forbidden_and_audited(): void
    {
        $salesUser = User::create([
            'name' => 'Citra Sales',
            'email' => 'citra.sales@astacode.com',
            'role' => 'sales',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);
        $salesRole = Role::where('name', 'sales')->first();
        if ($salesRole) {
            $salesUser->assignRole($salesRole);
        }

        $token = $salesUser->createToken('sales-mobile')->plainTextToken;

        // Attempt to access sensitive payroll endpoint
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/sensitive/salaries');

        // Must return 403 Forbidden RFC 7807 problem details
        $response->assertStatus(403)
            ->assertJsonPath('title', 'Forbidden Access')
            ->assertJsonPath('status', 403);

        // Verify immutable audit log recorded the unauthorized access attempt
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => (string) $salesUser->id,
            'action' => 'security.unauthorized_access',
            'entity_type' => 'salary_records',
            'is_sensitive_access' => true,
        ]);
    }

    /**
     * E2E-08: Authorized HR/Director accesses sensitive salary records -> 200 OK + Audit Log Recorded
     */
    public function test_e2e_08_hrd_manager_can_access_salary_with_audit_trail(): void
    {
        $hrUser = User::create([
            'name' => 'Fajar HRD',
            'email' => 'fajar.hrd@astacode.com',
            'role' => 'hrd_manager',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);
        $hrRole = Role::where('name', 'hrd_manager')->first();
        if ($hrRole) {
            $hrUser->assignRole($hrRole);
        }

        $token = $hrUser->createToken('hr-desktop')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/sensitive/salaries');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // Verify audit log recorded the authorized view
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => (string) $hrUser->id,
            'action' => 'audit.sensitive_data_view',
            'entity_type' => 'salary_records',
            'is_sensitive_access' => true,
        ]);
    }

    /**
     * E2E-08: Deal margin & HPP masking for roles lacking crm.margin.view
     */
    public function test_e2e_08_deal_margin_is_masked_for_sales(): void
    {
        $salesUser = User::create([
            'name' => 'Citra Sales',
            'email' => 'citra.sales2@astacode.com',
            'role' => 'sales',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);
        $salesRole = Role::where('name', 'sales')->first();
        if ($salesRole) {
            $salesUser->assignRole($salesRole);
        }
        $salesToken = $salesUser->createToken('sales')->plainTextToken;

        // Sales accesses deal -> HPP and margin are masked
        $salesResp = $this->withHeader('Authorization', 'Bearer ' . $salesToken)
            ->getJson('/api/sensitive/deal-margins/DEAL-101');

        $salesResp->assertStatus(200)
            ->assertJsonPath('data.hpp', '******')
            ->assertJsonPath('data.gross_margin', '******')
            ->assertJsonPath('data.margin_percentage', '******');
    }

    /**
     * E2E-08: Deal margin & HPP visible for Sales Manager
     */
    public function test_e2e_08_deal_margin_is_visible_for_sales_manager(): void
    {
        $managerUser = User::create([
            'name' => 'Bambang Manager',
            'email' => 'manager.sales@astacode.com',
            'role' => 'sales_manager',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);
        $managerRole = Role::where('name', 'sales_manager')->first();
        if ($managerRole) {
            $managerUser->assignRole($managerRole);
        }
        $managerToken = $managerUser->createToken('manager')->plainTextToken;

        // Manager accesses deal -> HPP and margin are visible
        $managerResp = $this->withHeader('Authorization', 'Bearer ' . $managerToken)
            ->getJson('/api/sensitive/deal-margins/DEAL-101');

        $managerResp->assertStatus(200)
            ->assertJsonPath('data.hpp', 165000000)
            ->assertJsonPath('data.gross_margin', 85000000)
            ->assertJsonPath('data.margin_percentage', '34%');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\User;
use App\Services\ApprovalEngineService;
use Astacode\Kernel\Outbox\OutboxEvent;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class ApprovalTest extends TestCase
{
    private ApprovalEngineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ApprovalEngineService();
    }

    public function test_serial_approval_workflow_and_outbox_event(): void
    {
        $requester = User::create([
            'name' => 'Citra Sales',
            'email' => 'sales@example.com',
            'role' => 'sales',
            'password' => Hash::make('secret'),
            'is_active' => true,
        ]);

        $manager = User::create([
            'name' => 'Bambang Manager',
            'email' => 'manager@example.com',
            'role' => 'sales_manager',
            'password' => Hash::make('secret'),
            'is_active' => true,
        ]);

        $director = User::create([
            'name' => 'Ahmad Director',
            'email' => 'director@example.com',
            'role' => 'director',
            'password' => Hash::make('secret'),
            'is_active' => true,
        ]);

        // Submit approval: Stage 1 = Manager, Stage 2 = Director
        $approval = $this->service->submit(
            referenceType: 'quotation',
            referenceId: 'QT-2026-0001',
            approvalType: 'discount',
            requesterId: $requester->id,
            stepsDefinition: [
                ['role' => 'sales_manager'],
                ['role' => 'director'],
            ],
            payload: ['discount_percentage' => 15]
        );

        $this->assertEquals('pending', $approval->status);
        $this->assertEquals(1, $approval->current_stage);
        $this->assertEquals(2, $approval->total_stages);

        // Step 1: Manager approves
        $approval = $this->service->decide(
            approval: $approval,
            approverId: $manager->id,
            action: 'approved',
            comments: 'Approved stage 1'
        );

        $this->assertEquals('pending', $approval->status);
        $this->assertEquals(2, $approval->current_stage);

        // Step 2: Director approves
        $approval = $this->service->decide(
            approval: $approval,
            approverId: $director->id,
            action: 'approved',
            comments: 'Final approval granted'
        );

        $this->assertEquals('approved', $approval->status);

        // Verify OutboxEvent was recorded
        $this->assertDatabaseHas('outbox_events', [
            'event_name' => 'approval.decided',
            'aggregate_id' => (string) $approval->id,
        ]);
    }

    public function test_segregation_of_duties_prevents_requester_from_approving(): void
    {
        $salesDirector = User::create([
            'name' => 'Director Who Created Quotation',
            'email' => 'self.approver@example.com',
            'role' => 'director',
            'password' => Hash::make('secret'),
            'is_active' => true,
        ]);

        $approval = $this->service->submit(
            referenceType: 'quotation',
            referenceId: 'QT-2026-0002',
            approvalType: 'discount',
            requesterId: $salesDirector->id,
            stepsDefinition: [
                ['role' => 'director'],
            ]
        );

        $this->expectException(\Illuminate\Http\Exceptions\HttpResponseException::class);

        // Cannot approve own request!
        $this->service->decide(
            approval: $approval,
            approverId: $salesDirector->id,
            action: 'approved'
        );
    }
}

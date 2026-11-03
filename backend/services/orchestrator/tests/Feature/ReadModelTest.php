<?php

namespace Tests\Feature;

use App\Models\ReadModel;
use App\Services\ReadModelBuilderService;
use Tests\TestCase;

class ReadModelTest extends TestCase
{
    private ReadModelBuilderService $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new ReadModelBuilderService();
    }

    public function test_projects_lead_captured_into_summary_read_model(): void
    {
        $payload = [
            'lead_id' => 'LD-2026-777',
            'name' => 'PT Sumber Rejeki',
            'stage' => 'new_lead',
        ];

        $this->builder->project('lead.captured', $payload, 'evt-lead-777');

        $this->assertDatabaseHas('read_models', [
            'model_type' => 'lead_summary',
            'model_key' => 'LD-2026-777',
            'version' => 1,
            'last_event_id' => 'evt-lead-777',
        ]);

        $model = ReadModel::where('model_type', 'lead_summary')->where('model_key', 'LD-2026-777')->first();
        $this->assertEquals('PT Sumber Rejeki', $model->data['name']);
        $this->assertEquals('new_lead', $model->data['stage']);
    }

    public function test_projects_deal_won_into_consolidated_deal_summary(): void
    {
        $payload = [
            'deal_id' => 'DEAL-999',
            'deal_code' => 'AST-2026-999',
            'title' => 'Implementasi ERP Cloud',
            'contract_value' => '350000000.00',
            'customer' => [
                'legal_name' => 'PT Nusantara Makmur',
            ],
            'won_at' => '2026-11-03T15:00:00Z',
        ];

        $this->builder->project('deal.won', $payload, 'evt-deal-999');

        $this->assertDatabaseHas('read_models', [
            'model_type' => 'deal_summary',
            'model_key' => 'DEAL-999',
        ]);

        $model = ReadModel::where('model_type', 'deal_summary')->where('model_key', 'DEAL-999')->first();
        $this->assertEquals('350000000.00', $model->data['contract_value']);
        $this->assertEquals('PT Nusantara Makmur', $model->data['customer_name']);
        $this->assertEquals('WON', $model->data['status']);
    }
}


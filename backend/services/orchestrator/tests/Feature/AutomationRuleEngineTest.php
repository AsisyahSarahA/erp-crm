<?php

namespace Tests\Feature;

use App\Models\AutomationRule;
use App\Models\AutomationRun;
use App\Services\AutomationEngineService;
use App\Services\JsonLogicEvaluator;
use Tests\TestCase;

class AutomationRuleEngineTest extends TestCase
{
    private AutomationEngineService $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new AutomationEngineService();
    }

    public function test_safe_json_logic_evaluator(): void
    {
        $context = [
            'status' => 'stale',
            'amount' => 75000000,
            'idle_days' => 6,
            'client' => ['tier' => 'VIP'],
        ];

        // Equality check
        $this->assertTrue(JsonLogicEvaluator::evaluate(
            ['==' => [['var' => 'status'], 'stale']],
            $context
        ));

        // Numeric comparison
        $this->assertTrue(JsonLogicEvaluator::evaluate(
            ['>' => [['var' => 'idle_days'], 5]],
            $context
        ));

        $this->assertFalse(JsonLogicEvaluator::evaluate(
            ['<' => [['var' => 'amount'], 50000000]],
            $context
        ));

        // Compound AND logic
        $this->assertTrue(JsonLogicEvaluator::evaluate(
            ['and' => [
                ['>' => [['var' => 'amount'], 50000000]],
                ['==' => [['var' => 'client.tier'], 'VIP']],
            ]],
            $context
        ));
    }

    public function test_lead_captured_triggers_round_robin_assignment(): void
    {
        $payload = [
            'lead_id' => 'LD-2026-9001',
            'name' => 'Calon Klien Prospektif',
            'source' => 'web_form',
            'contact_channel' => 'whatsapp',
        ];

        $results = $this->engine->trigger('event', 'lead.captured', $payload);

        $this->assertNotEmpty($results);

        // Verify round robin action was executed
        $rrRun = AutomationRun::where('trigger_type', 'event')->first();
        $this->assertNotNull($rrRun);
        $this->assertEquals('SUCCESS', $rrRun->status);
        $this->assertDatabaseHas('automation_runs', ['trigger_type' => 'event']);
    }

    public function test_stale_lead_idle_trigger_escalates_to_manager(): void
    {
        $payload = [
            'lead_id' => 'LD-2026-9002',
            'idle_days' => 7,
        ];

        $results = $this->engine->trigger('idle', null, $payload);

        $this->assertNotEmpty($results);

        $run = AutomationRun::where('trigger_type', 'idle')->latest('executed_at')->first();
        $this->assertNotNull($run);
        $this->assertEquals('SUCCESS', $run->status);

        $actions = $run->actions_executed;
        $this->assertEquals('escalate_stale_lead', $actions[0]['action']);
        $this->assertEquals('Sales Manager', $actions[0]['escalated_to']);
    }

    public function test_profit_margin_warning_triggers_on_low_margin(): void
    {
        $payload = [
            'deal_id' => 'DEAL-LOW-MARGIN',
            'margin_percentage' => 22, // Below 30% threshold
        ];

        $results = $this->engine->trigger('event', 'deal.won', $payload);

        $matchedRule = collect($results)->firstWhere('rule_name', 'Peringatan Margin Profit Kotor < 30%');
        $this->assertNotNull($matchedRule);
        $this->assertTrue($matchedRule['matched']);
        $this->assertEquals('SUCCESS', $matchedRule['status']);
    }
}


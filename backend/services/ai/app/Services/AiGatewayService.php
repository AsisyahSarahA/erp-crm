<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiBudget;
use App\Models\AiRun;
use App\Models\Prompt;
use App\Services\Guardrails\AiGuardrailService;
use App\Services\Providers\AiProviderManager;
use App\Services\Schemas\SchemaRegistry;
use Astacode\Kernel\Outbox\EventPublisher;
use Exception;
use Illuminate\Support\Facades\Log;

class AiGatewayService
{
    private AiProviderManager $providerManager;
    private AiGuardrailService $guardrails;

    public function __construct(
        ?AiProviderManager $providerManager = null,
        ?AiGuardrailService $guardrails = null
    ) {
        $this->providerManager = $providerManager ?? new AiProviderManager();
        $this->guardrails = $guardrails ?? new AiGuardrailService();
    }

    /**
     * Executes AI generation through standard Gateway pipeline.
     */
    public function run(
        string $module,
        string $purpose,
        array $payload,
        ?string $promptSlug = null,
        array $options = []
    ): AiRun {
        $inputJson = json_encode($payload);
        $inputHash = hash('sha256', $inputJson);

        // 1. Guardrail Check: Prompt Injection Detection
        $injectionCheck = $this->guardrails->detectInjection($inputJson);
        if (!$injectionCheck['is_safe']) {
            return AiRun::create([
                'module' => $module,
                'purpose' => $purpose,
                'provider' => 'none',
                'model' => 'guardrail',
                'input_hash' => $inputHash,
                'input_payload' => $payload,
                'status' => 'BLOCKED_BY_GUARDRAIL',
                'error_message' => $injectionCheck['reason'],
            ]);
        }

        // 2. Budget Check & Limiter per Module
        $budget = AiBudget::firstOrCreate(
            ['module' => $module],
            ['monthly_budget_usd' => 100.0, 'current_spend_usd' => 0.0]
        );

        if (!$budget->canSpend(0.001)) {
            return AiRun::create([
                'module' => $module,
                'purpose' => $purpose,
                'provider' => 'none',
                'model' => 'budget_limiter',
                'input_hash' => $inputHash,
                'input_payload' => $payload,
                'status' => 'BUDGET_EXCEEDED',
                'error_message' => "Budget biaya AI untuk modul '{$module}' telah habis (Limit: \${$budget->monthly_budget_usd}).",
            ]);
        }

        // 3. Resolve Prompt
        $prompt = null;
        $systemPrompt = "You are ASTACODE AI Assistant for enterprise software house management. Always respond in valid JSON.";
        $userPrompt = $inputJson;
        $schemaName = $options['schema_name'] ?? null;

        if ($promptSlug) {
            $prompt = Prompt::where('slug', $promptSlug)->where('status', 'active')->first();
            if ($prompt) {
                $systemPrompt = $prompt->system_prompt;
                $userPrompt = $prompt->render($payload);
                $schemaName = $schemaName ?? $prompt->schema_name;
            }
        }

        // Optional PII redaction
        if (!empty($options['redact_pii'])) {
            $userPrompt = $this->guardrails->redactPii($userPrompt);
        }

        // 4. Schema Resolution
        $jsonSchema = $schemaName ? SchemaRegistry::getSchema($schemaName) : null;
        $mergedOptions = array_merge($options, ['schema_name' => $schemaName]);

        // 5. Generate with Automated Provider Fallback
        $startTime = microtime(true);
        $completion = $this->providerManager->generateWithFallback(
            systemPrompt: $systemPrompt,
            userPrompt: $userPrompt,
            jsonSchema: $jsonSchema,
            options: $mergedOptions
        );
        $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

        // 6. Schema Validation & One-shot Auto Retry
        $outputData = is_array($completion['content']) ? $completion['content'] : json_decode((string) $completion['content'], true);
        if ($jsonSchema && is_array($outputData)) {
            $validation = SchemaRegistry::validate($outputData, $jsonSchema);
            if (!$validation['is_valid']) {
                // Retry once with repair prompt
                Log::warning("AI Schema validation failed: " . implode(', ', $validation['errors']) . ". Triggering retry.");
                $retryPrompt = "Fix and complete the following JSON to strictly adhere to schema: " . json_encode($outputData);
                $retryCompletion = $this->providerManager->generateWithFallback(
                    systemPrompt: $systemPrompt,
                    userPrompt: $retryPrompt,
                    jsonSchema: $jsonSchema,
                    options: $mergedOptions
                );
                $outputData = is_array($retryCompletion['content']) ? $retryCompletion['content'] : json_decode((string) $retryCompletion['content'], true);
            }
        }

        // 7. Persist Run Observability
        $aiRun = AiRun::create([
            'module' => $module,
            'purpose' => $purpose,
            'provider' => $completion['provider'] ?? 'mock',
            'model' => $completion['model'] ?? 'mock-deterministic',
            'prompt_slug' => $prompt?->slug,
            'prompt_version' => $prompt?->version,
            'input_hash' => $inputHash,
            'input_payload' => $payload,
            'output_json' => $outputData,
            'tokens_in' => $completion['tokens_in'] ?? 0,
            'tokens_out' => $completion['tokens_out'] ?? 0,
            'cost' => $completion['cost'] ?? 0.0000,
            'latency_ms' => $latencyMs,
            'status' => 'SUCCESS',
            'estimated_mandays' => $outputData['summary']['total_mandays'] ?? null,
        ]);

        // Record spend
        $budget->recordSpend((float) $aiRun->cost);

        // Emit outbox event
        EventPublisher::toOutbox(
            eventName: 'ai.run.completed',
            payload: [
                'run_id' => $aiRun->id,
                'module' => $aiRun->module,
                'purpose' => $aiRun->purpose,
                'provider' => $aiRun->provider,
                'status' => $aiRun->status,
            ],
            aggregateType: 'ai_run',
            aggregateId: (string) $aiRun->id
        );

        return $aiRun;
    }
}


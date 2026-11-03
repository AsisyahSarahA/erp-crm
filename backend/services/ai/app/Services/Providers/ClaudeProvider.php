<?php

declare(strict_types=1);

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;

class ClaudeProvider implements AiProviderInterface
{
    private ?string $apiKey;
    private string $defaultModel;

    public function __construct(?string $apiKey = null, string $defaultModel = 'claude-3-5-sonnet-20241022')
    {
        $this->apiKey = $apiKey ?? config('ai.claude_key', env('ANTHROPIC_API_KEY'));
        $this->defaultModel = $defaultModel;
    }

    public function getName(): string
    {
        return 'claude';
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    public function generate(
        string $systemPrompt,
        string $userPrompt,
        ?array $jsonSchema = null,
        array $options = []
    ): array {
        if (!$this->isAvailable()) {
            throw new \RuntimeException('Anthropic Claude API key is not configured.');
        }

        $model = $options['model'] ?? $this->defaultModel;
        $url = 'https://api.anthropic.com/v1/messages';

        $payload = [
            'model' => $model,
            'max_tokens' => 4096,
            'system' => $systemPrompt . "\nRespond ONLY in valid, strictly parsable JSON.",
            'messages' => [
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => $options['temperature'] ?? 0.2,
        ];

        $start = microtime(true);
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(30)->post($url, $payload);

        $latencyMs = (int) round((microtime(true) - $start) * 1000);

        if (!$response->successful()) {
            throw new \RuntimeException("Claude API error: " . $response->body());
        }

        $json = $response->json();
        $rawText = $json['content'][0]['text'] ?? '{}';
        $parsed = json_decode($rawText, true) ?? ['raw' => $rawText];

        $tokensIn = $json['usage']['input_tokens'] ?? 300;
        $tokensOut = $json['usage']['output_tokens'] ?? 500;

        return [
            'content' => $parsed,
            'tokens_in' => $tokensIn,
            'tokens_out' => $tokensOut,
            'cost' => ($tokensIn * 0.000003) + ($tokensOut * 0.000015),
            'latency_ms' => $latencyMs,
            'model' => $model,
            'provider' => 'claude',
        ];
    }
}


<?php

declare(strict_types=1);

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;

class OpenAiProvider implements AiProviderInterface
{
    private ?string $apiKey;
    private string $defaultModel;

    public function __construct(?string $apiKey = null, string $defaultModel = 'gpt-4o-mini')
    {
        $this->apiKey = $apiKey ?? config('ai.openai_key', env('OPENAI_API_KEY'));
        $this->defaultModel = $defaultModel;
    }

    public function getName(): string
    {
        return 'openai';
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
            throw new \RuntimeException('OpenAI API key is not configured.');
        }

        $model = $options['model'] ?? $this->defaultModel;
        $url = 'https://api.openai.com/v1/chat/completions';

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => $options['temperature'] ?? 0.2,
        ];

        $start = microtime(true);
        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($url, $payload);

        $latencyMs = (int) round((microtime(true) - $start) * 1000);

        if (!$response->successful()) {
            throw new \RuntimeException("OpenAI API error: " . $response->body());
        }

        $json = $response->json();
        $rawText = $json['choices'][0]['message']['content'] ?? '{}';
        $parsed = json_decode($rawText, true) ?? ['raw' => $rawText];

        $tokensIn = $json['usage']['prompt_tokens'] ?? 250;
        $tokensOut = $json['usage']['completion_tokens'] ?? 400;

        return [
            'content' => $parsed,
            'tokens_in' => $tokensIn,
            'tokens_out' => $tokensOut,
            'cost' => ($tokensIn * 0.00000015) + ($tokensOut * 0.0000006),
            'latency_ms' => $latencyMs,
            'model' => $model,
            'provider' => 'openai',
        ];
    }
}


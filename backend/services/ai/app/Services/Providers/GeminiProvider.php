<?php

declare(strict_types=1);

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;

class GeminiProvider implements AiProviderInterface
{
    private ?string $apiKey;
    private string $defaultModel;

    public function __construct(?string $apiKey = null, string $defaultModel = 'gemini-1.5-flash')
    {
        $this->apiKey = $apiKey ?? config('ai.gemini_key', env('GEMINI_API_KEY'));
        $this->defaultModel = $defaultModel;
    }

    public function getName(): string
    {
        return 'gemini';
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
            throw new \RuntimeException('Gemini API key is not configured.');
        }

        $model = $options['model'] ?? $this->defaultModel;
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => "System Instructions:\n{$systemPrompt}\n\nUser Request:\n{$userPrompt}"],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.2,
                'responseMimeType' => 'application/json',
            ],
        ];

        $start = microtime(true);
        $response = Http::timeout(30)->post($url, $payload);
        $latencyMs = (int) round((microtime(true) - $start) * 1000);

        if (!$response->successful()) {
            throw new \RuntimeException("Gemini API error: " . $response->body());
        }

        $json = $response->json();
        $rawText = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $parsed = json_decode($rawText, true) ?? ['raw' => $rawText];

        $tokensIn = $json['usageMetadata']['promptTokenCount'] ?? 250;
        $tokensOut = $json['usageMetadata']['candidatesTokenCount'] ?? 400;

        return [
            'content' => $parsed,
            'tokens_in' => $tokensIn,
            'tokens_out' => $tokensOut,
            'cost' => ($tokensIn * 0.000000075) + ($tokensOut * 0.0000003),
            'latency_ms' => $latencyMs,
            'model' => $model,
            'provider' => 'gemini',
        ];
    }
}


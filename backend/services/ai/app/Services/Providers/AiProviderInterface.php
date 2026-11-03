<?php

declare(strict_types=1);

namespace App\Services\Providers;

interface AiProviderInterface
{
    public function getName(): string;

    public function isAvailable(): bool;

    /**
     * Generates AI completion with optional structured JSON schema enforcement.
     *
     * @return array{content: mixed, tokens_in: int, tokens_out: int, cost: float, latency_ms: int, model: string}
     */
    public function generate(
        string $systemPrompt,
        string $userPrompt,
        ?array $jsonSchema = null,
        array $options = []
    ): array;
}


<?php

declare(strict_types=1);

namespace App\Services\Providers;

use Illuminate\Support\Facades\Log;

class AiProviderManager
{
    /** @var array<string, AiProviderInterface> */
    private array $providers = [];
    private MockDeterministicProvider $mockProvider;

    public function __construct(
        ?MockDeterministicProvider $mockProvider = null,
        ?GeminiProvider $gemini = null,
        ?ClaudeProvider $claude = null,
        ?OpenAiProvider $openai = null
    ) {
        $this->mockProvider = $mockProvider ?? new MockDeterministicProvider();
        $this->registerProvider($this->mockProvider);

        if ($gemini) {
            $this->registerProvider($gemini);
        } else {
            $this->registerProvider(new GeminiProvider());
        }

        if ($claude) {
            $this->registerProvider($claude);
        } else {
            $this->registerProvider(new ClaudeProvider());
        }

        if ($openai) {
            $this->registerProvider($openai);
        } else {
            $this->registerProvider(new OpenAiProvider());
        }
    }

    public function registerProvider(AiProviderInterface $provider): void
    {
        $this->providers[$provider->getName()] = $provider;
    }

    public function getProvider(string $name): ?AiProviderInterface
    {
        return $this->providers[$name] ?? null;
    }

    /**
     * @return array<string, array{name: string, is_available: bool}>
     */
    public function listProviders(): array
    {
        $list = [];
        foreach ($this->providers as $name => $provider) {
            $list[] = [
                'name' => $name,
                'is_available' => $provider->isAvailable(),
            ];
        }
        return $list;
    }

    /**
     * Executes completion with automated fallback chain across providers.
     */
    public function generateWithFallback(
        string $systemPrompt,
        string $userPrompt,
        ?array $jsonSchema = null,
        array $options = []
    ): array {
        $requested = $options['provider'] ?? 'gemini';
        $fallbackChain = $this->determineFallbackChain($requested);

        $lastException = null;

        foreach ($fallbackChain as $providerName) {
            $provider = $this->getProvider($providerName);
            if (!$provider || !$provider->isAvailable()) {
                continue;
            }

            try {
                $result = $provider->generate($systemPrompt, $userPrompt, $jsonSchema, $options);
                $result['fallback_used'] = ($providerName !== $requested);
                return $result;
            } catch (\Throwable $e) {
                Log::warning("AI Provider [{$providerName}] failed: " . $e->getMessage() . ". Falling back to next provider.");
                $lastException = $e;
            }
        }

        // Final fallback: deterministic mock provider to ensure high availability
        $mockResult = $this->mockProvider->generate($systemPrompt, $userPrompt, $jsonSchema, $options);
        $mockResult['fallback_used'] = true;
        $mockResult['fallback_reason'] = $lastException?->getMessage();

        return $mockResult;
    }

    /**
     * Builds preferred order of provider fallback.
     */
    private function determineFallbackChain(string $preferred): array
    {
        $order = [$preferred, 'gemini', 'claude', 'openai', 'mock'];
        return array_values(array_unique($order));
    }
}


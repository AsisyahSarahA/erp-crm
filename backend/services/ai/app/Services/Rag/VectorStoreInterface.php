<?php

declare(strict_types=1);

namespace App\Services\Rag;

interface VectorStoreInterface
{
    public function upsertDocument(string $id, string $text, array $metadata = []): void;

    /**
     * Searches similar documents using keyword/cosine relevance.
     *
     * @return array<array{id: string, text: string, score: float, metadata: array}>
     */
    public function search(string $query, int $topK = 5, ?string $customerId = null): array;
}


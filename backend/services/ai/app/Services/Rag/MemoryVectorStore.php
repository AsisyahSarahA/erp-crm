<?php

declare(strict_types=1);

namespace App\Services\Rag;

class MemoryVectorStore implements VectorStoreInterface
{
    /** @var array<string, array{id: string, text: string, metadata: array}> */
    private array $documents = [];

    public function upsertDocument(string $id, string $text, array $metadata = []): void
    {
        $this->documents[$id] = [
            'id' => $id,
            'text' => $text,
            'metadata' => $metadata,
        ];
    }

    public function search(string $query, int $topK = 5, ?string $customerId = null): array
    {
        $terms = array_filter(explode(' ', strtolower(trim($query))));
        $scored = [];

        foreach ($this->documents as $doc) {
            // Check customer isolation
            if ($customerId !== null && isset($doc['metadata']['customer_id'])) {
                if ($doc['metadata']['customer_id'] !== $customerId) {
                    continue;
                }
            }

            $docText = strtolower($doc['text']);
            $score = 0.0;
            foreach ($terms as $term) {
                if (str_contains($docText, $term)) {
                    $score += 1.0;
                }
            }

            if ($score > 0) {
                $scored[] = [
                    'id' => $doc['id'],
                    'text' => $doc['text'],
                    'score' => $score,
                    'metadata' => $doc['metadata'],
                ];
            }
        }

        usort($scored, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($scored, 0, $topK);
    }
}


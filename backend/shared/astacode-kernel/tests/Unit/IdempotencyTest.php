<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Models\IdempotencyKey;
use PHPUnit\Framework\TestCase;

class IdempotencyTest extends TestCase
{
    public function test_idempotency_statuses(): void
    {
        $this->assertSame('PROCESSING', IdempotencyKey::STATUS_PROCESSING);
        $this->assertSame('COMPLETED', IdempotencyKey::STATUS_COMPLETED);
        $this->assertSame('FAILED', IdempotencyKey::STATUS_FAILED);
    }

    public function test_request_hash_generation(): void
    {
        $content1 = json_encode(['foo' => 'bar']);
        $content2 = json_encode(['foo' => 'baz']);

        $this->assertNotSame(hash('sha256', $content1), hash('sha256', $content2));
        $this->assertSame(hash('sha256', $content1), hash('sha256', $content1));
    }
}


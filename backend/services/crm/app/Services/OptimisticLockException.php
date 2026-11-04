<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

class OptimisticLockException extends RuntimeException
{
    public function __construct(
        public readonly int $expectedVersion,
        public readonly int $actualVersion,
        string $message = ''
    ) {
        parent::__construct(
            $message !== ''
                ? $message
                : "Konflik versi RAB (Optimistic Lock): versi yang dikirim ({$expectedVersion}) sudah usang karena versi terbaru di server adalah v{$actualVersion}. Silakan muat ulang data RAB."
        );
    }
}


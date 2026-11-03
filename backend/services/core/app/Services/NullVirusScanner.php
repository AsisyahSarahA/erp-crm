<?php

declare(strict_types=1);

namespace App\Services;

class NullVirusScanner implements VirusScannerInterface
{
    public function scan(string $filePath): array
    {
        return [
            'is_clean' => true,
            'threat' => null,
        ];
    }
}


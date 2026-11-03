<?php

declare(strict_types=1);

namespace App\Services;

interface VirusScannerInterface
{
    /**
     * Scans a file path for viruses or malware.
     * @return array{is_clean: bool, threat: ?string}
     */
    public function scan(string $filePath): array;
}


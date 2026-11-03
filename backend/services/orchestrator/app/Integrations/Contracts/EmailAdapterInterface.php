<?php

declare(strict_types=1);

namespace App\Integrations\Contracts;

interface EmailAdapterInterface
{
    public function sendEmail(string $recipientEmail, string $subject, string $body, array $attachments = []): array;
}


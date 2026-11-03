<?php

declare(strict_types=1);

namespace App\Integrations\Contracts;

interface WhatsAppAdapterInterface
{
    public function sendMessage(string $recipientPhone, string $templateName, array $parameters = []): array;
}


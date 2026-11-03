<?php

declare(strict_types=1);

namespace App\Integrations\Contracts;

interface TelegramAdapterInterface
{
    public function sendAlert(string $chatId, string $message, string $parseMode = 'Markdown'): array;
}


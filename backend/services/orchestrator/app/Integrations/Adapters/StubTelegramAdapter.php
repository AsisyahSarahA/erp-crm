<?php

declare(strict_types=1);

namespace App\Integrations\Adapters;

use App\Integrations\Contracts\TelegramAdapterInterface;
use Illuminate\Support\Facades\Log;

class StubTelegramAdapter implements TelegramAdapterInterface
{
    public function sendAlert(string $chatId, string $message, string $parseMode = 'Markdown'): array
    {
        Log::info("Stub Telegram sent to chat {$chatId}: {$message}");

        return [
            'status' => 'sent',
            'provider' => 'stub-telegram',
            'message_id' => 'tg-msg-' . str()->uuid(),
            'chat_id' => $chatId,
        ];
    }
}


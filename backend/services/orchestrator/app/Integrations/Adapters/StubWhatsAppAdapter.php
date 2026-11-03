<?php

declare(strict_types=1);

namespace App\Integrations\Adapters;

use App\Integrations\Contracts\WhatsAppAdapterInterface;
use Illuminate\Support\Facades\Log;

class StubWhatsAppAdapter implements WhatsAppAdapterInterface
{
    public function sendMessage(string $recipientPhone, string $templateName, array $parameters = []): array
    {
        Log::info("Stub WhatsApp sent to {$recipientPhone} with template {$templateName}", $parameters);

        return [
            'status' => 'sent',
            'provider' => 'stub-wa',
            'message_id' => 'wa-msg-' . str()->uuid(),
            'recipient' => $recipientPhone,
        ];
    }
}


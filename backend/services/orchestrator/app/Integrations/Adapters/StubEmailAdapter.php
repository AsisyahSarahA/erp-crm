<?php

declare(strict_types=1);

namespace App\Integrations\Adapters;

use App\Integrations\Contracts\EmailAdapterInterface;
use Illuminate\Support\Facades\Log;

class StubEmailAdapter implements EmailAdapterInterface
{
    public function sendEmail(string $recipientEmail, string $subject, string $body, array $attachments = []): array
    {
        Log::info("Stub Email sent to {$recipientEmail}: {$subject}");

        return [
            'status' => 'sent',
            'provider' => 'stub-mail',
            'message_id' => 'mail-msg-' . str()->uuid(),
            'recipient' => $recipientEmail,
        ];
    }
}


<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $subscriptions = [
            [
                'event_name' => 'deal.won',
                'consumer_service' => 'pm-service',
                'target_endpoint' => 'http://localhost:8005/api/internal/events/deal-won',
                'is_active' => true,
                'max_retries' => 5,
            ],
            [
                'event_name' => 'deal.won',
                'consumer_service' => 'finance-service',
                'target_endpoint' => 'http://localhost:8006/api/internal/events/deal-won',
                'is_active' => true,
                'max_retries' => 5,
            ],
            [
                'event_name' => 'deal.won',
                'consumer_service' => 'hrd-service',
                'target_endpoint' => 'http://localhost:8004/api/internal/events/deal-won',
                'is_active' => true,
                'max_retries' => 5,
            ],
            [
                'event_name' => 'deal.won',
                'consumer_service' => 'crm-service',
                'target_endpoint' => 'http://localhost:8002/api/internal/events/deal-won',
                'is_active' => true,
                'max_retries' => 5,
            ],
            [
                'event_name' => 'lead.captured',
                'consumer_service' => 'crm-service',
                'target_endpoint' => 'http://localhost:8002/api/internal/events/lead-captured',
                'is_active' => true,
                'max_retries' => 5,
            ],
            [
                'event_name' => 'invoice.paid',
                'consumer_service' => 'pm-service',
                'target_endpoint' => 'http://localhost:8005/api/internal/events/invoice-paid',
                'is_active' => true,
                'max_retries' => 5,
            ],
        ];

        foreach ($subscriptions as $sub) {
            Subscription::firstOrCreate(
                [
                    'event_name' => $sub['event_name'],
                    'consumer_service' => $sub['consumer_service'],
                ],
                $sub
            );
        }
    }
}


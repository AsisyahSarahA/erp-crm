<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'customer_id' => null,
            'type' => 'FOLLOW_UP',
            'title' => 'Follow up pertama prospek baru (SLA ≤ 30 menit)',
            'notes' => 'Hubungi via WhatsApp untuk discovery call',
            'start_at' => now(),
            'end_at' => now()->addMinutes(30),
            'sla_due_at' => now()->addMinutes(30),
            'status' => 'SCHEDULED',
            'outcome' => null,
            'created_by' => 1,
        ];
    }
}


<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    private static int $sequence = 100;

    public function definition(): array
    {
        self::$sequence++;
        $year = date('Y');
        $code = sprintf('LD-%s-%04d', $year, self::$sequence);

        return [
            'code' => $code,
            'lead_code' => $code,
            'customer_id' => Customer::factory(),
            'title' => 'Implementasi Sistem ' . $this->faker->words(3, true),
            'project_type' => $this->faker->randomElement(Lead::PROJECT_TYPES),
            'priority' => $this->faker->randomElement(Lead::PRIORITIES),
            'stage' => 'NEW_LEAD',
            'stage_id' => 'new_lead',
            'source' => 'WEBSITE',
            'sub_source' => 'Contact Form',
            'est_value' => $this->faker->numberBetween(50, 500) * 1000000,
            'win_probability' => 10,
            'sales_pic_id' => 10,
            'owner_id' => 10,
            'team_id' => 1,
            'sales_pic_name' => 'Citra Sales Executive',
            'score' => 65,
            'score_label' => 'WARM',
            'days_in_stage' => 1,
            'is_stale' => false,
            'has_next_step' => true,
            'consent' => true,
            'consent_at' => now(),
        ];
    }
}


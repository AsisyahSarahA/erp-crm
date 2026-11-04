<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\LeadSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadSource>
 */
class LeadSourceFactory extends Factory
{
    protected $model = LeadSource::class;

    public function definition(): array
    {
        $word = strtoupper($this->faker->unique()->lexify('SRC_????'));

        return [
            'code' => $word,
            'name' => 'Channel ' . $word,
            'category' => 'INBOUND',
            'channel_group' => 'INBOUND',
            'sub_sources' => ['Campaign A', 'Campaign B'],
            'is_active' => true,
            'sort_order' => 1,
        ];
    }
}


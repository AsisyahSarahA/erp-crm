<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    private static int $sequence = 100;

    public function definition(): array
    {
        self::$sequence++;
        $code = sprintf('CUS-%04d', self::$sequence);
        $companyName = 'PT ' . $this->faker->company() . ' Nusantara';

        return [
            'code' => $code,
            'customer_code' => $code,
            'legal_name' => $companyName,
            'brand_name' => $this->faker->companySuffix() . ' Tech',
            'type' => $this->faker->randomElement(Customer::TYPES),
            'industry' => $this->faker->randomElement(['TECH_SAAS', 'FINANCE_BANKING', 'MFG_LOGISTICS', 'HEALTHCARE', 'RETAIL_ECOMMERCE']),
            'size' => $this->faker->randomElement(['11-50', '51-200', '201-500', '500+']),
            'website' => 'https://' . $this->faker->domainName(),
            'npwp' => sprintf(
                '%02d.%03d.%03d.%d-%03d.0000',
                $this->faker->numberBetween(1, 99),
                $this->faker->numberBetween(100, 999),
                $this->faker->numberBetween(100, 999),
                $this->faker->numberBetween(1, 9),
                $this->faker->numberBetween(100, 999)
            ),
            'nib' => (string) $this->faker->numerify('#############'),
            'is_pkp' => true,
            'is_pph23_withholder' => true,
            'status' => 'prospect',
            'owner_id' => 1,
            'team_id' => 1,
            'company_id' => 1,
            'health_score' => 85,
            'billing_address' => [
                'street' => $this->faker->streetAddress(),
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12950',
                'country' => 'ID',
            ],
            'phone' => '+62812' . $this->faker->numerify('########'),
            'email' => $this->faker->unique()->companyEmail(),
        ];
    }
}


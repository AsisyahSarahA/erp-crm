<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        $phone = '+62813' . $this->faker->numerify('########');
        $title = $this->faker->randomElement(['Chief Technology Officer', 'VP Engineering', 'Finance Manager', 'Procurement Lead']);

        return [
            'customer_id' => Customer::factory(),
            'name' => $this->faker->name(),
            'role' => $this->faker->randomElement(Contact::ROLES),
            'title' => $title,
            'position' => $title,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $phone,
            'phone_wa' => $phone,
            'is_primary' => false,
            'consent_comm' => true,
            'consent_at' => now(),
            'preferred_channel' => 'whatsapp',
            'department' => 'IT & Digital',
        ];
    }
}


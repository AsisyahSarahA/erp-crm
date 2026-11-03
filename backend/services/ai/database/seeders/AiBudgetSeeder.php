<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiBudget;
use Illuminate\Database\Seeder;

class AiBudgetSeeder extends Seeder
{
    public function run(): void
    {
        $budgets = [
            ['module' => 'crm', 'monthly_budget_usd' => 150.0, 'rate_limit_rpm' => 60],
            ['module' => 'pm', 'monthly_budget_usd' => 100.0, 'rate_limit_rpm' => 60],
            ['module' => 'marketing', 'monthly_budget_usd' => 150.0, 'rate_limit_rpm' => 60],
            ['module' => 'hrd', 'monthly_budget_usd' => 50.0, 'rate_limit_rpm' => 30],
            ['module' => 'finance', 'monthly_budget_usd' => 50.0, 'rate_limit_rpm' => 30],
        ];

        foreach ($budgets as $b) {
            AiBudget::firstOrCreate(
                ['module' => $b['module']],
                array_merge($b, ['current_spend_usd' => 0.0, 'is_blocked' => false])
            );
        }
    }
}


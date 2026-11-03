<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiBudget extends Model
{
    protected $fillable = [
        'module',
        'monthly_budget_usd',
        'current_spend_usd',
        'rate_limit_rpm',
        'is_blocked',
        'reset_at',
    ];

    protected $casts = [
        'monthly_budget_usd' => 'float',
        'current_spend_usd' => 'float',
        'rate_limit_rpm' => 'integer',
        'is_blocked' => 'boolean',
        'reset_at' => 'datetime',
    ];

    public function canSpend(float $amount = 0.001): bool
    {
        if ($this->is_blocked) {
            return false;
        }

        return ($this->current_spend_usd + $amount) <= $this->monthly_budget_usd;
    }

    public function recordSpend(float $cost): void
    {
        $this->current_spend_usd += $cost;
        if ($this->current_spend_usd >= $this->monthly_budget_usd) {
            $this->is_blocked = true;
        }
        $this->save();
    }
}


<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Cache;

final class LeadAutoAssignService
{
    private const SALES_TEAM_POOL = [
        ['id' => 10, 'name' => 'Citra Sales Executive', 'team_id' => 1],
        ['id' => 11, 'name' => 'Dedi Sales Representative', 'team_id' => 1],
        ['id' => 12, 'name' => 'Eko Senior Account Executive', 'team_id' => 1],
        ['id' => 13, 'name' => 'Fani Business Development', 'team_id' => 1],
    ];

    private const ENTERPRISE_MANAGER = [
        'id' => 1,
        'name' => 'Bambang Sales Manager',
        'team_id' => 1,
    ];

    /**
     * Resolves the assigned Sales PIC based on input, rules, or round-robin distribution.
     *
     * @param array $input Payload from lead creation
     * @return array [owner_id, sales_pic_name, team_id, strategy]
     */
    public function assign(array $input): array
    {
        // 1. Manual Assignment if explicitly provided
        $manualId = $input['owner_id'] ?? $input['sales_pic_id'] ?? null;
        if (!empty($manualId)) {
            $repName = $input['sales_pic_name'] ?? null;
            if (!$repName) {
                $found = collect(self::SALES_TEAM_POOL)->firstWhere('id', (int) $manualId);
                $repName = $found ? $found['name'] : "Sales User #{$manualId}";
            }

            return [
                'owner_id' => (int) $manualId,
                'sales_pic_id' => (int) $manualId,
                'sales_pic_name' => $repName,
                'team_id' => (int) ($input['team_id'] ?? 1),
                'strategy' => 'MANUAL',
            ];
        }

        // 2. Rule-based Assignment for Enterprise Opportunities (≥ Rp 300.000.000)
        $estValue = (float) ($input['est_value'] ?? 0);
        if ($estValue >= 300000000) {
            return [
                'owner_id' => self::ENTERPRISE_MANAGER['id'],
                'sales_pic_id' => self::ENTERPRISE_MANAGER['id'],
                'sales_pic_name' => self::ENTERPRISE_MANAGER['name'],
                'team_id' => self::ENTERPRISE_MANAGER['team_id'],
                'strategy' => 'RULE_BASED_ENTERPRISE',
            ];
        }

        // 3. Round-Robin Distribution among Active Sales Team Pool
        $pool = self::SALES_TEAM_POOL;
        $poolSize = count($pool);

        // Atomic round-robin index using cache
        $currentIndex = (int) Cache::get('crm:lead_assign:round_robin_index', 0);
        $assignedRep = $pool[$currentIndex % $poolSize];

        Cache::put('crm:lead_assign:round_robin_index', ($currentIndex + 1) % $poolSize, now()->addDays(30));

        return [
            'owner_id' => $assignedRep['id'],
            'sales_pic_id' => $assignedRep['id'],
            'sales_pic_name' => $assignedRep['name'],
            'team_id' => $assignedRep['team_id'],
            'strategy' => 'ROUND_ROBIN',
        ];
    }
}


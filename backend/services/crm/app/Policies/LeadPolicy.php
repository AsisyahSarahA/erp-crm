<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lead;

class LeadPolicy
{
    private const UNRESTRICTED_ROLES = [
        'super_admin',
        'super admin',
        'owner',
        'direktur',
        'sales_manager',
        'sales manager',
        'presales',
        'finance',
        'finance_ar',
        'finance ar specialist',
        'pm',
        'project_manager',
    ];

    public function hasUnrestrictedAccess(array $context): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        return !empty(array_intersect($roles, self::UNRESTRICTED_ROLES))
            || in_array('crm.lead.view_all', $permissions, true)
            || in_array('*', $permissions, true);
    }

    public function view(array $context, Lead $lead): bool
    {
        if ($this->hasUnrestrictedAccess($context)) {
            return true;
        }

        $userId = isset($context['id']) ? (int) $context['id'] : null;
        $teamId = isset($context['team_id']) ? (int) $context['team_id'] : null;
        $teamMemberIds = array_map('intval', (array) ($context['team_member_ids'] ?? []));

        if ($userId !== null) {
            if ((int) $lead->owner_id === $userId || (int) $lead->sales_pic_id === $userId) {
                return true;
            }
            if ($lead->created_by !== null && (string) $lead->created_by === (string) $userId) {
                return true;
            }
            if ($lead->watchers()->where('user_id', $userId)->exists()) {
                return true;
            }
        }

        if ($teamId !== null && $lead->team_id !== null && (int) $lead->team_id === $teamId) {
            return true;
        }

        if (!empty($teamMemberIds) && $lead->owner_id !== null && in_array((int) $lead->owner_id, $teamMemberIds, true)) {
            return true;
        }

        return false;
    }

    public function update(array $context, Lead $lead): bool
    {
        return $this->view($context, $lead);
    }

    public function delete(array $context, Lead $lead): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        return !empty(array_intersect($roles, ['super_admin', 'super admin', 'owner', 'direktur', 'sales_manager', 'sales manager']))
            || in_array('crm.lead.delete', $permissions, true)
            || in_array('*', $permissions, true);
    }

    public function reopen(array $context, Lead $lead): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        return !empty(array_intersect($roles, ['super_admin', 'super admin', 'owner', 'direktur', 'sales_manager', 'sales manager']))
            || in_array('crm.lead.reopen', $permissions, true)
            || in_array('*', $permissions, true);
    }
}


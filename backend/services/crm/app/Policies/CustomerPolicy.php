<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;

class CustomerPolicy
{
    private const UNRESTRICTED_ROLES = [
        'super_admin',
        'super admin',
        'owner',
        'direktur',
        'sales_manager',
        'sales manager',
        'finance',
        'finance_ar',
        'finance ar specialist',
        'legal_counsel',
        'legal counsel',
        'pm',
        'project_manager',
    ];

    private const NPWP_UNMASKED_ROLES = [
        'super_admin',
        'super admin',
        'owner',
        'direktur',
        'finance',
        'finance_ar',
        'finance ar specialist',
        'legal_counsel',
        'legal counsel',
    ];

    private const NPWP_VERIFIER_ROLES = [
        'super_admin',
        'super admin',
        'owner',
        'direktur',
        'finance',
        'finance_ar',
        'finance ar specialist',
    ];

    public function hasUnrestrictedAccess(array $context): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        return !empty(array_intersect($roles, self::UNRESTRICTED_ROLES))
            || in_array('crm.customer.view_all', $permissions, true)
            || in_array('*', $permissions, true);
    }

    /**
     * Determine whether the user can view a specific Customer.
     * Sales can only view their own customers or customers belonging to their team.
     */
    public function view(array $context, Customer $customer): bool
    {
        if ($this->hasUnrestrictedAccess($context)) {
            return true;
        }

        $userId = isset($context['id']) ? (int) $context['id'] : null;
        $teamId = isset($context['team_id']) ? (int) $context['team_id'] : null;
        $teamMemberIds = array_map('intval', (array) ($context['team_member_ids'] ?? []));

        if ($userId !== null) {
            if ((int) $customer->owner_id === $userId) {
                return true;
            }
            if ($customer->created_by !== null && (string) $customer->created_by === (string) $userId) {
                return true;
            }
            if ($customer->leads()->where('sales_pic_id', $userId)->exists()) {
                return true;
            }
        }

        if ($teamId !== null && $customer->team_id !== null && (int) $customer->team_id === $teamId) {
            return true;
        }

        if (!empty($teamMemberIds) && $customer->owner_id !== null && in_array((int) $customer->owner_id, $teamMemberIds, true)) {
            return true;
        }

        return false;
    }

    public function update(array $context, Customer $customer): bool
    {
        return $this->view($context, $customer);
    }

    public function delete(array $context, Customer $customer): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        if (
            !empty(array_intersect($roles, ['super_admin', 'super admin', 'owner', 'direktur', 'sales_manager', 'sales manager']))
            || in_array('crm.customer.delete', $permissions, true)
            || in_array('*', $permissions, true)
        ) {
            return true;
        }

        return false;
    }

    public function canViewUnmaskedNpwp(array $context): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        return !empty(array_intersect($roles, self::NPWP_UNMASKED_ROLES))
            || in_array('crm.npwp.view_unmasked', $permissions, true)
            || in_array('finance.tax.view', $permissions, true)
            || in_array('finance.npwp.verify', $permissions, true)
            || in_array('crm.customer.verify_npwp', $permissions, true)
            || in_array('*', $permissions, true);
    }

    public function verifyNpwp(array $context, Customer $customer): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        return !empty(array_intersect($roles, self::NPWP_VERIFIER_ROLES))
            || in_array('finance.npwp.verify', $permissions, true)
            || in_array('crm.customer.verify_npwp', $permissions, true)
            || in_array('*', $permissions, true);
    }

    public function merge(array $context, Customer $masterCustomer): bool
    {
        $permissions = (array) ($context['permissions'] ?? []);
        if (in_array('crm.customer.merge', $permissions, true)) {
            return true;
        }

        return $this->view($context, $masterCustomer);
    }

    public function anonymize(array $context, Customer $customer): bool
    {
        $roles = array_map('strtolower', (array) ($context['roles'] ?? [$context['role'] ?? '']));
        $permissions = (array) ($context['permissions'] ?? []);

        return !empty(array_intersect($roles, ['super_admin', 'super admin', 'owner', 'direktur', 'legal_counsel', 'legal counsel']))
            || in_array('crm.customer.anonymize', $permissions, true)
            || in_array('*', $permissions, true);
    }
}


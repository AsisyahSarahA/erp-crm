import { describe, it, expect, beforeEach } from 'vitest'
import { render, screen } from '@testing-library/react'
import '@testing-library/jest-dom/vitest'
import React from 'react'
import { Can } from '../shared/auth/Can'
import { useAuthStore } from '../shared/auth/authStore'

describe('Permission Guard (<Can />)', () => {
  beforeEach(() => {
    useAuthStore.setState({
      user: null,
      token: null,
      isAuthenticated: false,
    })
  })

  it('renders children when user has the required permission', () => {
    useAuthStore.setState({
      user: {
        id: 1,
        name: 'Sales Rep',
        email: 'sales@astacode.com',
        roles: ['sales'],
        permissions: ['crm.lead.create', 'crm.lead.view'],
      },
      isAuthenticated: true,
    })

    render(
      <Can permission="crm.lead.create" fallback={<div>No Access</div>}>
        <div>Create Lead Button</div>
      </Can>
    )

    expect(screen.getByText('Create Lead Button')).toBeInTheDocument()
    expect(screen.queryByText('No Access')).not.toBeInTheDocument()
  })

  it('renders fallback when user lacks the required permission', () => {
    useAuthStore.setState({
      user: {
        id: 2,
        name: 'Junior Dev',
        email: 'dev@astacode.com',
        roles: ['developer'],
        permissions: ['pm.task.update'],
      },
      isAuthenticated: true,
    })

    render(
      <Can permission="crm.lead.delete" fallback={<div>No Access</div>}>
        <div>Delete Button</div>
      </Can>
    )

    expect(screen.getByText('No Access')).toBeInTheDocument()
    expect(screen.queryByText('Delete Button')).not.toBeInTheDocument()
  })

  it('always allows super_admin regardless of explicit permissions', () => {
    useAuthStore.setState({
      user: {
        id: 99,
        name: 'Super Admin',
        email: 'admin@astacode.com',
        roles: ['super_admin'],
        permissions: [],
      },
      isAuthenticated: true,
    })

    render(
      <Can permission="finance.payout.approve" fallback={<div>No Access</div>}>
        <div>Approve Payout</div>
      </Can>
    )

    expect(screen.getByText('Approve Payout')).toBeInTheDocument()
  })
})

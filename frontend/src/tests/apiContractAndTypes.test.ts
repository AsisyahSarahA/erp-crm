import { describe, it, expect } from 'vitest';
import { queryKeys } from '../shared/api-client/generated/hooks';
import { apiClient, ApiError } from '../shared/api-client/client';
import type { Lead, Customer, Invoice, Project, Employee, ApiProblemDetails } from '../shared/api-client/generated/api-types';

describe('Unified API Contract, Types and Hooks', () => {
  it('exports apiClient instance with base configuration', () => {
    expect(apiClient).toBeDefined();
    expect(typeof apiClient.get).toBe('function');
    expect(typeof apiClient.post).toBe('function');
    expect(typeof apiClient.put).toBe('function');
    expect(typeof apiClient.delete).toBe('function');
  });

  it('correctly constructs TanStack Query cache keys', () => {
    expect(queryKeys.health).toEqual(['gateway', 'health']);
    expect(queryKeys.currentUser).toEqual(['core', 'auth', 'me']);

    const leadParams = { stage: 'NEW_LEAD', sort: '-created_at' };
    expect(queryKeys.leads(leadParams)).toEqual(['crm', 'leads', leadParams]);
    expect(queryKeys.lead(42)).toEqual(['crm', 'lead', 42]);

    expect(queryKeys.customers()).toEqual(['crm', 'customers', undefined]);
    expect(queryKeys.projects()).toEqual(['pm', 'projects', undefined]);
    expect(queryKeys.invoices()).toEqual(['finance', 'invoices', undefined]);
    expect(queryKeys.employees()).toEqual(['hrd', 'employees', undefined]);
  });

  it('verifies generated schema type structures', () => {
    const sampleLead: Lead = {
      id: 1,
      lead_code: 'LD-2026-0001',
      title: 'Aplikasi ERP Logistik',
      project_type: 'ERP_CUSTOM',
      priority: 'HOT',
      stage: 'NEW_LEAD',
      source: 'WEBSITE',
      est_value: 150000000,
      win_probability: 10,
      created_at: '2026-03-01T00:00:00Z',
    };

    const sampleCustomer: Customer = {
      id: 101,
      customer_code: 'CUS-0001',
      legal_name: 'PT Megah Nusantara Teknologi',
      customer_type: 'PERUSAHAAN',
      industry: 'TECH_SAAS',
      phone: '+6281234567890',
      status: 'ACTIVE',
      health_score: 95,
    };

    const sampleInvoice: Invoice = {
      id: 201,
      invoice_number: 'INV/2026/02/0001',
      customer_name: 'PT Megah Nusantara',
      term_number: 1,
      term_name: 'Termin 1 (DP 30%)',
      dpp_amount: 75000000,
      vat_amount: 8250000,
      total_bill: 83250000,
      net_cash_receivable: 81750000,
      status: 'PAID',
      issue_date: '2026-02-05',
      due_date: '2026-02-19',
    };

    const sampleProject: Project = {
      id: 301,
      project_code: 'PRJ-001',
      customer_name: 'PT Megah Nusantara',
      title: 'Aplikasi HRD & Presensi GPS',
      project_type: 'ERP_CUSTOM',
      status: 'ACTIVE',
      health: 'GREEN',
      contract_value: 250000000,
      budget_mandays: 110,
      consumed_mandays: 45.5,
      start_date: '2026-02-15',
      target_end_date: '2026-05-30',
    };

    const sampleEmployee: Employee = {
      id: 401,
      nik: 'EMP-0001',
      full_name: 'Ahmad Fauzi',
      email: 'director@astacode.com',
      phone: '+628110000002',
      employment_status: 'TETAP',
      job_title: 'Direktur Utama',
      department: 'Executive',
      technical_role: 'SALES',
    };

    expect(sampleLead.stage).toBe('NEW_LEAD');
    expect(sampleCustomer.status).toBe('ACTIVE');
    expect(sampleInvoice.total_bill).toBe(83250000);
    expect(sampleProject.status).toBe('ACTIVE');
    expect(sampleEmployee.employment_status).toBe('TETAP');
  });

  it('handles RFC 7807 problem details in ApiError', () => {
    const problem: ApiProblemDetails = {
      type: 'https://astacode.com/errors/validation-failed',
      title: 'Unprocessable Entity',
      status: 422,
      detail: 'Judul lead wajib diisi minimal 2 karakter.',
      errors: {
        title: ['Judul lead wajib diisi minimal 2 karakter.'],
      },
      correlation_id: 'test-uuid-1234',
    };

    const error = new ApiError(422, problem);
    expect(error.name).toBe('ApiError');
    expect(error.status).toBe(422);
    expect(error.problem.correlation_id).toBe('test-uuid-1234');
    expect(error.fieldErrors.title).toContain('Judul lead wajib diisi minimal 2 karakter.');
  });
});

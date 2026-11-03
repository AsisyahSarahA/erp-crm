<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin' => 'Super Administrator',
            'director' => 'Owner / Direktur Utama',
            'sales_manager' => 'Sales Manager',
            'sales' => 'Sales Executive',
            'presales' => 'Presales & Solution Architect',
            'marketing_lead' => 'Marketing Lead',
            'content_creator' => 'Content Creator',
            'hrd_manager' => 'HR Manager',
            'hrd_staff' => 'HR Staff',
            'project_manager' => 'Project Manager',
            'tech_lead' => 'Technical Lead',
            'developer' => 'Software Engineer',
            'qa_engineer' => 'QA Engineer',
            'finance_manager' => 'Finance Manager',
            'finance_staff' => 'Finance Staff',
            'finance_ar' => 'Finance AR Specialist',
            'finance_ar_specialist' => 'Finance AR Specialist',
            'legal_counsel' => 'Legal Counsel',
            'client' => 'Client Portal User',
        ];

        foreach ($roles as $name => $displayName) {
            Role::firstOrCreate(['name' => $name], [
                'display_name' => $displayName,
                'guard_name' => 'web',
            ]);
        }

        $permissionsByModule = [
            'crm' => [
                'crm.lead.view' => 'Lihat Data Leads',
                'crm.lead.create' => 'Tambah Lead Baru',
                'crm.lead.update' => 'Ubah Data Lead',
                'crm.lead.delete' => 'Hapus Lead',
                'crm.margin.view' => 'Lihat Margin & HPP Internal RAB',
                'crm.discount.approve' => 'Setujui Diskon Penawaran',
                'crm.top.lock' => 'Kunci Termin Pembayaran TOP',
                'crm.clause.edit' => 'Edit Klausul Khusus MoU',
                'crm.stage.override' => 'Override Gate Transisi Stage Pipeline',
                'crm.contract.view' => 'Lihat Kontrak & SLA',
                'crm.contract.edit' => 'Edit Dokumen Kontrak & Addendum',
                'crm.voicenote.record' => 'Rekam Voice Note Presales',
                'crm.wa.chat' => 'Kirim Pesan WhatsApp Client Care',
            ],
            'marketing' => [
                'marketing.trend.view' => 'Lihat Riset Tren Konten',
                'marketing.content.create' => 'Buat Konten Pemasaran',
                'marketing.content.approve' => 'Setujui Konten Publikasi',
                'marketing.content.publish' => 'Tayangkan Konten ke Sosmed',
                'marketing.ads.manage' => 'Kelola Anggaran Iklan Ads',
                'marketing.inbox.manage' => 'Kelola Omnichannel Inbox',
            ],
            'hrd' => [
                'hrd.employee.view' => 'Lihat Data Pegawai',
                'hrd.employee.manage' => 'Kelola Master Data Pegawai',
                'hrd.salary.view' => 'Lihat Struktur Gaji & Honor',
                'hrd.payroll.manage' => 'Hitung & Proses Payroll',
                'hrd.payroll.approve' => 'Persetujuan Final Rilis Payroll',
                'hrd.attendance.manage' => 'Kelola Presensi & Geofence',
                'hrd.leave.approve' => 'Setujui Pengajuan Cuti & Izin',
                'hrd.ats.manage' => 'Kelola Rekrutmen & Pelamar',
                'hrd.intern.manage' => 'Kelola Kuota & Program PKL/Magang',
            ],
            'pm' => [
                'pm.project.view' => 'Lihat Proyek & Backlog',
                'pm.project.create' => 'Buat Proyek Baru dari Deal Won',
                'pm.project.manage' => 'Kelola Pengaturan Proyek',
                'pm.kanban.manage' => 'Kelola Kartu Kanban & Sprint',
                'pm.sprint.manage' => 'Start & Complete Sprint',
                'pm.qa.manage' => 'Kelola Test Run QA',
                'pm.uat.signoff' => 'Sign-off Dokumen UAT Milestone',
                'pm.ticket.manage' => 'Kelola Tiket Service Desk',
                'pm.timesheet.manage' => 'Verifikasi Jam Kerja Timelog',
            ],
            'finance' => [
                'finance.coa.manage' => 'Kelola Chart of Accounts',
                'finance.journal.view' => 'Lihat Jurnal Akuntansi',
                'finance.journal.create' => 'Buat Jurnal Manual & Koreksi',
                'finance.invoice.issue' => 'Terbitkan Faktur Tagihan Invoice',
                'finance.invoice.pay' => 'Verifikasi Pembayaran Invoice',
                'finance.expense.verify' => 'Verifikasi Klaim Struk Pengeluaran',
                'finance.report.view' => 'Lihat Laporan P&L & Arus Kas',
                'finance.bank.manage' => 'Kelola Rekening Bank & Buku Kas',
            ],
            'core' => [
                'core.user.manage' => 'Kelola Pengguna Sistem',
                'core.role.manage' => 'Kelola Hak Akses Role & Permission',
                'core.audit.view' => 'Lihat Audit Trail Lengkap',
                'core.setting.manage' => 'Kelola Pengaturan Sistem',
                'core.webhook.manage' => 'Kelola Webhook & Integrasi',
                'core.impersonate' => 'Impersonate Pengguna Lain',
            ],
        ];

        foreach ($permissionsByModule as $module => $perms) {
            foreach ($perms as $name => $displayName) {
                Permission::firstOrCreate(['name' => $name], [
                    'display_name' => $displayName,
                    'module' => $module,
                    'guard_name' => 'web',
                ]);
            }
        }

        // Assign default permission sets
        $roleDirector = Role::where('name', 'director')->first();
        if ($roleDirector) {
            $roleDirector->permissions()->sync(Permission::all());
        }

        $roleSales = Role::where('name', 'sales')->first();
        if ($roleSales) {
            $salesPerms = Permission::whereIn('name', [
                'crm.lead.view', 'crm.lead.create', 'crm.lead.update',
                'crm.voicenote.record', 'crm.wa.chat', 'crm.contract.view'
            ])->get();
            $roleSales->permissions()->sync($salesPerms);
        }

        $roleSalesManager = Role::where('name', 'sales_manager')->first();
        if ($roleSalesManager) {
            $smPerms = Permission::where('module', 'crm')->get();
            $roleSalesManager->permissions()->sync($smPerms);
        }

        $roleFinance = Role::where('name', 'finance_manager')->first();
        if ($roleFinance) {
            $finPerms = Permission::where('module', 'finance')
                ->orWhereIn('name', ['crm.lead.view', 'crm.margin.view', 'crm.top.lock', 'hrd.salary.view'])
                ->get();
            $roleFinance->permissions()->sync($finPerms);
        }
    }
}

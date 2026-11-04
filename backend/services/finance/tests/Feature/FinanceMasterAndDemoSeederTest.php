<?php

namespace Tests\Feature;

use Database\Seeders\BankAccountSeeder;
use Database\Seeders\ChartOfAccountsSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\FinanceDemoSeeder;
use Database\Seeders\PostingRuleSeeder;
use Database\Seeders\TaxSettingSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FinanceMasterAndDemoSeederTest extends TestCase
{
    public function test_finance_master_seeders_run_and_are_idempotent(): void
    {
        $this->seed(DatabaseSeeder::class);

        $initialCoa = DB::table('fin_chart_of_accounts')->count();
        $initialRules = DB::table('fin_posting_rules')->count();
        $initialTaxes = DB::table('fin_tax_settings')->count();
        $initialBanks = DB::table('fin_bank_accounts')->count();

        $this->assertEquals(23, $initialCoa);
        $this->assertGreaterThanOrEqual(10, $initialRules);
        $this->assertEquals(3, $initialTaxes);
        $this->assertEquals(2, $initialBanks);

        // Run second time to verify idempotency
        $this->seed(DatabaseSeeder::class);

        $this->assertEquals(23, DB::table('fin_chart_of_accounts')->count());
        $this->assertEquals($initialRules, DB::table('fin_posting_rules')->count());
        $this->assertEquals(3, DB::table('fin_tax_settings')->count());
        $this->assertEquals(2, DB::table('fin_bank_accounts')->count());
    }

    public function test_chart_of_accounts_strictly_matches_lampiran_a_23_accounts(): void
    {
        $this->seed(ChartOfAccountsSeeder::class);

        $accounts = DB::table('fin_chart_of_accounts')->get();
        $this->assertCount(23, $accounts);

        // Expected Lampiran A accounts mapping
        $expected = [
            '1010' => ['name' => 'Kas/Bank Operasional (BCA)', 'category' => 'ASET', 'normal' => 'D'],
            '1020' => ['name' => 'Kas Kecil (Petty Cash)', 'category' => 'ASET', 'normal' => 'D'],
            '1030' => ['name' => 'Piutang Usaha', 'category' => 'ASET', 'normal' => 'D'],
            '1040' => ['name' => 'Uang Muka Pajak PPh 23', 'category' => 'ASET', 'normal' => 'D'],
            '2010' => ['name' => 'Utang Vendor', 'category' => 'KEWAJIBAN', 'normal' => 'K'],
            '2020' => ['name' => 'Utang PPN', 'category' => 'KEWAJIBAN', 'normal' => 'K'],
            '2030' => ['name' => 'Utang Gaji', 'category' => 'KEWAJIBAN', 'normal' => 'K'],
            '2040' => ['name' => 'Pendapatan Diterima Dimuka (DP)', 'category' => 'KEWAJIBAN', 'normal' => 'K'],
            '2050' => ['name' => 'Utang BPJS & PPh 21/23/26', 'category' => 'KEWAJIBAN', 'normal' => 'K'],
            '3010' => ['name' => 'Modal Disetor', 'category' => 'EKUITAS', 'normal' => 'K'],
            '3020' => ['name' => 'Laba Ditahan', 'category' => 'EKUITAS', 'normal' => 'K'],
            '4010' => ['name' => 'Pendapatan Jasa Pengembangan', 'category' => 'PENDAPATAN', 'normal' => 'K'],
            '4020' => ['name' => 'Pendapatan Cloud/SLA', 'category' => 'PENDAPATAN', 'normal' => 'K'],
            '4030' => ['name' => 'Pendapatan Maintenance', 'category' => 'PENDAPATAN', 'normal' => 'K'],
            '5010' => ['name' => 'HPP — Direct Cost Proyek', 'category' => 'HPP', 'normal' => 'D'],
            '5020' => ['name' => 'HPP — Subkon & Freelancer', 'category' => 'HPP', 'normal' => 'D'],
            '5030' => ['name' => 'HPP — Cloud & Server Proyek', 'category' => 'HPP', 'normal' => 'D'],
            '5040' => ['name' => 'HPP — Direct Labor (dari timelog)', 'category' => 'HPP', 'normal' => 'D'],
            '6010' => ['name' => 'Beban Gaji & Upah Tetap', 'category' => 'OPEX', 'normal' => 'D'],
            '6020' => ['name' => 'Beban Sewa Gedung', 'category' => 'OPEX', 'normal' => 'D'],
            '6030' => ['name' => 'Beban Langganan Software/SaaS', 'category' => 'OPEX', 'normal' => 'D'],
            '6040' => ['name' => 'Beban Utilitas & Operasional Kantor', 'category' => 'OPEX', 'normal' => 'D'],
            '6050' => ['name' => 'Beban Pemasaran & Iklan', 'category' => 'OPEX', 'normal' => 'D'],
        ];

        foreach ($expected as $code => $spec) {
            $acc = $accounts->firstWhere('code', $code);
            $this->assertNotNull($acc, "Akun COA {$code} harus terdaftar");
            $this->assertEquals($spec['name'], $acc->name);
            $this->assertEquals($spec['category'], $acc->category);
            $this->assertEquals($spec['normal'], $acc->normal_balance);
        }
    }

    public function test_posting_rules_map_to_valid_coa_accounts(): void
    {
        $this->seed(ChartOfAccountsSeeder::class);
        $this->seed(PostingRuleSeeder::class);

        $rules = DB::table('fin_posting_rules')->get();
        $validCodes = DB::table('fin_chart_of_accounts')->pluck('code')->toArray();

        foreach ($rules as $rule) {
            $this->assertContains($rule->debit_account_code, $validCodes, "Debit account {$rule->debit_account_code} must exist in COA");
            $this->assertContains($rule->credit_account_code, $validCodes, "Credit account {$rule->credit_account_code} must exist in COA");
        }
    }

    public function test_finance_demo_seeder_creates_balanced_journal_entries(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(FinanceDemoSeeder::class);

        // Check invoices
        $invoices = DB::table('fin_invoices')->get();
        $this->assertGreaterThanOrEqual(5, $invoices->count());

        // Check journal entries
        $journals = DB::table('fin_journal_entries')->get();
        $this->assertGreaterThanOrEqual(4, $journals->count());

        foreach ($journals as $j) {
            $this->assertEquals(
                $j->total_debit,
                $j->total_credit,
                "Jurnal {$j->entry_number} harus seimbang: Debit {$j->total_debit} != Credit {$j->total_credit}"
            );

            // Verify lines sum matches entry totals
            $debitSum = DB::table('fin_journal_lines')
                ->where('journal_entry_id', $j->id)
                ->where('entry_type', 'DEBIT')
                ->sum('amount');

            $creditSum = DB::table('fin_journal_lines')
                ->where('journal_entry_id', $j->id)
                ->where('entry_type', 'CREDIT')
                ->sum('amount');

            $this->assertEquals((float) $j->total_debit, (float) $debitSum);
            $this->assertEquals((float) $j->total_credit, (float) $creditSum);
        }
    }
}


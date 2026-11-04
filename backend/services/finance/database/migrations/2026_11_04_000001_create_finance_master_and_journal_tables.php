<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chart of Accounts (COA 23 Akun, Lampiran A)
        Schema::create('fin_chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique(); // e.g. 1010, 1020, 2040, etc.
            $table->string('name', 255);
            $table->string('category', 64); // ASET, KEWAJIBAN, EKUITAS, PENDAPATAN, HPP, OPEX
            $table->string('normal_balance', 1); // D or K
            $table->boolean('is_original_prd')->default(true); // true = 18 asli, false = tambahan (2040, 2050, 5040, 6050)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Automated Posting Rules (§10.5)
        Schema::create('fin_posting_rules', function (Blueprint $table) {
            $table->id();
            $table->string('event_code', 64)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('debit_account_code', 16);
            $table->string('credit_account_code', 16);
            $table->json('secondary_rules')->nullable(); // For multi-line entries (PPN, PPh 23 splits)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Tax Settings
        Schema::create('fin_tax_settings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 128);
            $table->decimal('rate_pct', 7, 4)->default(0);
            $table->decimal('fixed_amount_idr', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Bank Accounts & Cash
        Schema::create('fin_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 32)->unique();
            $table->string('bank_name', 128);
            $table->string('account_number', 64);
            $table->string('account_holder', 255);
            $table->string('coa_code', 16);
            $table->decimal('balance', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Invoices (Receivable Invoicing)
        Schema::create('fin_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 64)->unique(); // INV/YYYY/MM/NNNN
            $table->string('customer_id', 64)->index();
            $table->string('customer_name', 255);
            $table->string('project_id', 64)->nullable()->index();
            $table->string('project_title', 255)->nullable();
            $table->unsignedInteger('term_number')->default(1);
            $table->string('term_name', 128);
            $table->decimal('dpp_amount', 18, 2)->default(0);
            $table->decimal('vat_rate', 7, 4)->default(11.0);
            $table->decimal('vat_amount', 18, 2)->default(0);
            $table->decimal('pph23_rate', 7, 4)->default(2.0);
            $table->decimal('pph23_amount', 18, 2)->default(0);
            $table->decimal('total_bill', 18, 2)->default(0); // DPP + PPN
            $table->decimal('net_cash_receivable', 18, 2)->default(0); // DPP + PPN - PPh23
            $table->string('status', 32)->default('DRAFT'); // DRAFT, ISSUED, PAID, OVERDUE, CANCELLED
            $table->date('issue_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. General Journal Entries (Jurnal Umum Otomatis & Manual)
        Schema::create('fin_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number', 64)->unique(); // JRN-{AR|AP|HR|OPEX|TRF}/YYYY/MM/NNNN
            $table->string('source_type', 32); // AR, AP, HR, OPEX, TRF, MANUAL
            $table->string('reference_number', 64)->nullable(); // INV/..., SPK/..., etc.
            $table->date('date');
            $table->text('description');
            $table->decimal('total_debit', 18, 2);
            $table->decimal('total_credit', 18, 2);
            $table->boolean('is_balanced')->default(true);
            $table->boolean('is_posted')->default(true);
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });

        // 7. General Journal Lines
        Schema::create('fin_journal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('fin_journal_entries')->cascadeOnDelete();
            $table->string('account_code', 16);
            $table->string('account_name', 255);
            $table->string('entry_type', 10); // DEBIT, CREDIT
            $table->decimal('amount', 18, 2);
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_journal_lines');
        Schema::dropIfExists('fin_journal_entries');
        Schema::dropIfExists('fin_invoices');
        Schema::dropIfExists('fin_bank_accounts');
        Schema::dropIfExists('fin_tax_settings');
        Schema::dropIfExists('fin_posting_rules');
        Schema::dropIfExists('fin_chart_of_accounts');
    }
};


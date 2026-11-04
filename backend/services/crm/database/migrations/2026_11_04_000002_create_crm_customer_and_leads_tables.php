<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Customers (Master Customer 360)
        Schema::create('crm_customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 32)->unique(); // CUS-NNNN
            $table->string('legal_name', 255);
            $table->string('brand_name', 255)->nullable();
            $table->string('customer_type', 32)->default('PERUSAHAAN');
            $table->string('industry', 64)->nullable();
            $table->string('npwp', 32)->nullable();
            $table->text('billing_address')->nullable();
            $table->string('phone', 32);
            $table->string('email', 255)->nullable();
            $table->string('status', 32)->default('PROSPECT'); // PROSPECT, ACTIVE, CHURNED
            $table->unsignedInteger('health_score')->default(100);
            $table->string('created_by', 64)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Contacts
        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('crm_customers')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('position', 128)->nullable();
            $table->string('phone', 32);
            $table->string('email', 255)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 3. Leads (FR-CRM-01)
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code', 32)->unique(); // LD-YYYY-NNNN
            $table->foreignId('customer_id')->nullable()->constrained('crm_customers')->nullOnDelete();
            $table->string('title', 255);
            $table->string('project_type', 64);
            $table->string('priority', 32)->default('MEDIUM'); // HOT, HIGH, MEDIUM, LOW
            $table->string('stage', 64)->default('NEW_LEAD');
            $table->string('source', 64);
            $table->string('sub_source', 128)->nullable();
            $table->decimal('est_value', 18, 2)->default(0);
            $table->unsignedInteger('win_probability')->default(10);
            $table->unsignedBigInteger('sales_pic_id')->nullable();
            $table->string('sales_pic_name', 255)->nullable();
            $table->string('reject_reason_code', 64)->nullable();
            $table->text('reject_notes')->nullable();
            $table->date('target_closing')->nullable();
            $table->unsignedInteger('days_in_stage')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Quotations
        Schema::create('crm_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('crm_leads')->cascadeOnDelete();
            $table->string('quotation_number', 64)->unique(); // QT/YYYY/MM/NNNN-Rn
            $table->unsignedInteger('version')->default(1);
            $table->string('status', 32)->default('DRAFT'); // DRAFT, SENT, ACCEPTED, REJECTED
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('contingency_pct', 7, 4)->default(10.0);
            $table->decimal('discount_pct', 7, 4)->default(0);
            $table->decimal('dpp', 18, 2)->default(0);
            $table->decimal('vat_rate', 7, 4)->default(11.0);
            $table->decimal('vat_amount', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);
            $table->text('total_terbilang')->nullable();
            $table->decimal('hpp_internal', 18, 2)->default(0);
            $table->decimal('margin_pct', 7, 4)->default(0);
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_quotations');
        Schema::dropIfExists('crm_leads');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_customers');
    }
};


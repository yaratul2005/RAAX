<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vds_certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('finance_invoice_id');
            $table->string('certificate_number');
            $table->bigInteger('withheld_amount_cents');
            $table->date('deposit_date');
            $table->enum('status', ['issued', 'cancelled'])->default('issued');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'certificate_number']);
            $table->foreign('finance_invoice_id')->references('id')->on('finance_invoices')->onDelete('cascade');
        });

        Schema::create('credit_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('sales_order_id');
            $table->string('note_number');
            $table->string('original_tax_invoice_number');
            $table->bigInteger('returned_amount_cents');
            $table->bigInteger('adjusted_vat_cents');
            $table->enum('status', ['applied', 'void'])->default('applied');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'note_number']);
            // Using cascade to avoid dependency issues on cleanup, or restrict depending on design
            // Since sales_orders might be managed by Sales module, hard FK might break loose coupling if we wanted it strictly separated.
            // But they are both in the same DB, so we can use standard foreign.
        });

        Schema::create('debit_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('purchase_order_id');
            $table->string('note_number');
            $table->string('original_purchase_invoice_number');
            $table->bigInteger('returned_amount_cents');
            $table->bigInteger('adjusted_vat_cents');
            $table->enum('status', ['applied', 'void'])->default('applied');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'note_number']);
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['vds_certificates', 'credit_notes', 'debit_notes'];
            foreach ($tables as $table) {
                DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
                DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY;");
                DB::statement("
                    CREATE POLICY tenant_isolation_policy ON {$table}
                    FOR ALL
                    TO app_user
                    USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
                ");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('debit_notes');
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('vds_certificates');
    }
};

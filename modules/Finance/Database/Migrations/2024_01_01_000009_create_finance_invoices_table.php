<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->enum('type', ['AP', 'AR']);
            $table->string('invoice_number');
            $table->uuid('party_id');
            $table->date('issue_date');
            $table->date('due_date');
            $table->bigInteger('amount_cents');
            $table->bigInteger('paid_cents')->default(0);
            $table->string('currency_code', 3)->default('USD'); // ISO 4217
            $table->enum('status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->timestamps();
            $table->softDeletes();
        });

        if (config('database.default') !== 'sqlite') {
            DB::statement('ALTER TABLE finance_invoices ENABLE ROW LEVEL SECURITY;');
            DB::statement('ALTER TABLE finance_invoices FORCE ROW LEVEL SECURITY;');
            DB::statement("
                CREATE POLICY tenant_isolation_policy ON finance_invoices
                FOR ALL
                TO app_user
                USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_invoices');
    }
};

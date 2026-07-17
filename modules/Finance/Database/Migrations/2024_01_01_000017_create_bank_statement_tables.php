<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('bank_name');
            $table->string('account_number');
            $table->date('statement_date');
            $table->bigInteger('opening_balance_cents');
            $table->bigInteger('closing_balance_cents');
            $table->enum('status', ['draft', 'reconciled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('bank_statement_id');
            $table->date('transaction_date');
            $table->string('reference');
            $table->bigInteger('amount_cents');
            $table->boolean('is_reconciled')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bank_statement_id')->references('id')->on('bank_statements')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['bank_statements', 'bank_statement_lines'];
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
        Schema::dropIfExists('bank_statement_lines');
        Schema::dropIfExists('bank_statements');
    }
};

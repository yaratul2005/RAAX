<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('from_currency', 3);
            $table->string('to_currency', 3)->default('BDT');
            $table->bigInteger('rate_basis_points'); // 12050 = 120.50
            $table->date('effective_date');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'from_currency', 'to_currency', 'effective_date'], 'unique_tenant_currency_date');
        });

        Schema::create('forex_revaluation_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('fiscal_year_id')->nullable(); // Using nullable in case they haven't explicitly set up a fiscal year structure
            $table->string('revaluation_month'); // YYYY-MM
            $table->bigInteger('unrealized_gain_loss_cents');
            $table->uuid('journal_entry_id');
            $table->timestamps();
            $table->softDeletes();
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['currency_exchange_rates', 'forex_revaluation_logs'];
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
        Schema::dropIfExists('forex_revaluation_logs');
        Schema::dropIfExists('currency_exchange_rates');
    }
};

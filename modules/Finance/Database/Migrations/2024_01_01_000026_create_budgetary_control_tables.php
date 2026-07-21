<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('fiscal_year_id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onDelete('cascade');
        });

        Schema::create('budget_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id'); // added for consistent RLS application
            $table->uuid('budget_id');
            $table->uuid('chart_of_accounts_id');
            $table->bigInteger('allocated_amount_cents');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('budget_id')->references('id')->on('budgets')->onDelete('cascade');
            $table->foreign('chart_of_accounts_id')->references('id')->on('ledger_accounts')->onDelete('cascade');
            $table->unique(['budget_id', 'chart_of_accounts_id']);
        });

        Schema::create('encumbrance_ledgers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('chart_of_accounts_id');
            $table->string('source_type');
            $table->uuid('source_id');
            $table->bigInteger('encumbered_amount_cents');
            $table->bigInteger('relieved_amount_cents')->default(0);
            $table->enum('status', ['active', 'relieved', 'cancelled'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('chart_of_accounts_id')->references('id')->on('ledger_accounts')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['budgets', 'budget_lines', 'encumbrance_ledgers'];
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
        Schema::dropIfExists('encumbrance_ledgers');
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('budgets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_jurisdictions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('country_code', 2); // ISO 3166-1 alpha-2
            $table->string('currency_code', 3); // ISO 4217
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tax_rate_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('tax_jurisdiction_id');
            $table->string('name');
            $table->enum('type', ['standard', 'reduced', 'zero_rated'])->default('standard');
            $table->integer('rate_basis_points'); // e.g. 1800 for 18%
            $table->date('effective_from');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tax_jurisdiction_id')->references('id')->on('tax_jurisdictions')->onDelete('cascade');
        });

        Schema::create('tax_ledger_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('tax_jurisdiction_id');
            $table->string('source_type');
            $table->uuid('source_id');
            $table->uuid('tax_rate_rule_id');
            $table->bigInteger('base_amount_cents');
            $table->bigInteger('tax_amount_cents');
            $table->timestamp('posted_at');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tax_jurisdiction_id')->references('id')->on('tax_jurisdictions')->onDelete('cascade');
            $table->foreign('tax_rate_rule_id')->references('id')->on('tax_rate_rules')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['tax_jurisdictions', 'tax_rate_rules', 'tax_ledger_entries'];
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
        Schema::dropIfExists('tax_ledger_entries');
        Schema::dropIfExists('tax_rate_rules');
        Schema::dropIfExists('tax_jurisdictions');
    }
};

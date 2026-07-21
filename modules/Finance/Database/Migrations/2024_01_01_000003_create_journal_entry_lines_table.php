<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('journal_entry_id');
            $table->uuid('account_id')->nullable();
            $table->uuid('ledger_account_id')->nullable();
            $table->bigInteger('debit_cents')->default(0)->comment('Stored in cents');
            $table->bigInteger('credit_cents')->default(0)->comment('Stored in cents');
            $table->bigInteger('debit_amount')->default(0)->comment('Stored in cents');
            $table->bigInteger('credit_amount')->default(0)->comment('Stored in cents');
            $table->timestamps();
            $table->softDeletes();
        });

        if (config('database.default') !== 'sqlite') {
            DB::statement('ALTER TABLE journal_entry_lines ENABLE ROW LEVEL SECURITY;');
            DB::statement('ALTER TABLE journal_entry_lines FORCE ROW LEVEL SECURITY;');
            DB::statement("
                CREATE POLICY tenant_isolation_policy ON journal_entry_lines
                FOR ALL
                TO app_user
                USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};

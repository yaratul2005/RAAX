<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('parent_id')->nullable();
            $table->string('code');
            $table->string('name');
            $table->enum('type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            $table->boolean('is_reconcilable')->default(false);
            $table->char('currency_code', 3);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            DB::statement('ALTER TABLE chart_of_accounts ENABLE ROW LEVEL SECURITY;');
            DB::statement('ALTER TABLE chart_of_accounts FORCE ROW LEVEL SECURITY;');
            DB::statement("
                CREATE POLICY tenant_isolation_policy ON chart_of_accounts
                FOR ALL
                TO app_user
                USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};

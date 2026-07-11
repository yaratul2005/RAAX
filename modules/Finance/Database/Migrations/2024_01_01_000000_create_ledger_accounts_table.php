<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id');
            $table->string('account_code');
            $table->string('account_name');
            $table->string('account_type');
            $table->char('currency_code', 3);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE ledger_accounts ENABLE ROW LEVEL SECURITY;');
        DB::statement('ALTER TABLE ledger_accounts FORCE ROW LEVEL SECURITY;');
        DB::statement("
            CREATE POLICY tenant_isolation_policy ON ledger_accounts
            FOR ALL
            TO app_user
            USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_accounts');
    }
};

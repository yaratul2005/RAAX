<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_chains', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('journal_entry_id');
            $table->bigInteger('sequence_number'); // tenant-specific auto-increment handled in app layer
            $table->string('payload_hash', 64); // SHA-256 is 64 chars hex
            $table->string('chain_hash', 64);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'sequence_number']);
            $table->unique(['tenant_id', 'journal_entry_id']);
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            DB::statement("ALTER TABLE ledger_chains ENABLE ROW LEVEL SECURITY;");
            DB::statement("ALTER TABLE ledger_chains FORCE ROW LEVEL SECURITY;");
            DB::statement("
                CREATE POLICY tenant_isolation_policy ON ledger_chains
                FOR ALL
                TO app_user
                USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_chains');
    }
};

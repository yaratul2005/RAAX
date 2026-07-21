<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->date('entry_date')->nullable();
            $table->date('date')->nullable();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('amount')->default(0)->comment('Stored in cents');
            $table->char('currency_code', 3)->default('BDT');
            $table->timestamps();
            $table->softDeletes();
        });

        if (config('database.default') !== 'sqlite') {
            DB::statement('ALTER TABLE journal_entries ENABLE ROW LEVEL SECURITY;');
        }
        if (config('database.default') !== 'sqlite') {
            DB::statement('ALTER TABLE journal_entries FORCE ROW LEVEL SECURITY;');
        }
        if (config('database.default') !== 'sqlite') {
            DB::statement("
            CREATE POLICY tenant_isolation_policy ON journal_entries
            FOR ALL
            TO app_user
            USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
        ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};

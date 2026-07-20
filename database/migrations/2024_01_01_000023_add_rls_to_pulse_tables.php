<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pulse tables typically: pulse_values, pulse_entries, pulse_aggregates, pulse_dicts
        $pulseTables = ['pulse_values', 'pulse_entries', 'pulse_aggregates'];

        foreach ($pulseTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'tenant_id')) {
                        $table->uuid('tenant_id')->nullable();
                        // It's nullable for initial migration, in production we'd backfill or make required if always under tenant context
                    }
                });

                if (config('database.default') !== 'sqlite') {
                    DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
                    DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY;");
                    DB::statement("
                        CREATE POLICY tenant_isolation_policy ON {$table}
                        FOR ALL
                        TO app_user
                        USING (
                            tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID
                            OR tenant_id IS NULL
                        );
                    ");
                }
            }
        }
    }

    public function down(): void
    {
        // Down migration
    }
};

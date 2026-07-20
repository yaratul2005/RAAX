<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add tenant_id to Pulse tables
        $pulseTables = [
            'pulse_values',
            'pulse_entries',
            'pulse_aggregates',
        ];

        foreach ($pulseTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->uuid('tenant_id')->nullable();
                });

                if (config('database.default') !== 'sqlite') {
                    DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
                    DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY;");
                    DB::statement("
                        CREATE POLICY tenant_isolation_policy ON {$table}
                        FOR ALL
                        TO app_user
                        USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID OR tenant_id IS NULL);
                    ");
                }
            }
        }
    }

    public function down(): void
    {
        // Dropping tenant_id from pulse tables
        $pulseTables = [
            'pulse_values',
            'pulse_entries',
            'pulse_aggregates',
        ];

        foreach ($pulseTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('tenant_id');
                });
            }
        }
    }
};

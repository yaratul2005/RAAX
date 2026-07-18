<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intercompany_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id'); // Source branch
            $table->uuid('destination_tenant_id'); // Destination branch
            $table->string('transfer_number');
            $table->bigInteger('total_cost_cents')->default(0);
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->enum('status', ['draft', 'in_transit', 'received', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'transfer_number']);
        });

        Schema::create('intercompany_transfer_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id'); // Must have tenant_id for RLS
            $table->uuid('intercompany_transfer_id');
            $table->string('item_sku');
            $table->integer('qty');
            $table->bigInteger('unit_transfer_cost_cents')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('intercompany_transfer_id')->references('id')->on('intercompany_transfers')->onDelete('cascade');
        });

        Schema::create('mushak_6_5_challans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('intercompany_transfer_id');
            $table->string('challan_number');
            $table->string('vehicle_number');
            $table->string('driver_name');
            $table->timestamp('declared_at');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'challan_number']);
            $table->foreign('intercompany_transfer_id')->references('id')->on('intercompany_transfers')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['intercompany_transfers', 'intercompany_transfer_lines', 'mushak_6_5_challans'];
            foreach ($tables as $table) {
                DB::statement("ALTER TABLE {$table} ENABLE ROW LEVEL SECURITY;");
                DB::statement("ALTER TABLE {$table} FORCE ROW LEVEL SECURITY;");
                DB::statement("
                    CREATE POLICY tenant_isolation_policy ON {$table}
                    FOR ALL
                    TO app_user
                    USING (
                        tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID
                        OR
                        (
                            '{$table}' = 'intercompany_transfers' AND
                            destination_tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID
                        )
                    );
                ");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mushak_6_5_challans');
        Schema::dropIfExists('intercompany_transfer_lines');
        Schema::dropIfExists('intercompany_transfers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('warehouse_bins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id'); // Add tenant_id for consistent RLS
            $table->uuid('warehouse_id');
            $table->string('bin_label');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });

        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('purchase_order_id')->nullable();
            $table->uuid('received_by');
            $table->string('grn_number');
            $table->enum('status', ['draft', 'verified', 'returned'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'grn_number']);
            // Decoupled cross-module reference. We won't use hard foreign key to POs to keep strict database boundaries if they were microservices, but for monolithic DB we can. However, strict decoupling suggests omitting DB-level FK across module boundaries. We'll omit it here and enforce in logic.
        });

        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('warehouse_bin_id');
            $table->string('item_sku');
            $table->uuid('purchase_order_id')->nullable();
            $table->integer('original_qty');
            $table->integer('remaining_qty');
            $table->bigInteger('unit_cost_cents');
            $table->string('currency_code', 3)->default('BDT');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('warehouse_bin_id')->references('id')->on('warehouse_bins')->onDelete('restrict');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('inventory_batch_id');
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('qty'); // absolute quantity moved
            $table->string('reason');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('inventory_batch_id')->references('id')->on('inventory_batches')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['warehouses', 'warehouse_bins', 'goods_received_notes', 'inventory_batches', 'stock_movements'];
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
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('goods_received_notes');
        Schema::dropIfExists('warehouse_bins');
        Schema::dropIfExists('warehouses');
    }
};

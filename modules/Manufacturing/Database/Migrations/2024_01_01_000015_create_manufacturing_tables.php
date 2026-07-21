<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_of_materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('finished_item_sku');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->uuid('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('bom_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id'); // Required for RLS isolation even if child
            $table->uuid('bill_of_materials_id');
            $table->string('raw_item_sku');
            $table->integer('qty_required');
            $table->integer('wastage_allowance_percentage_cents')->default(0); // e.g. 250 = 2.50%
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bill_of_materials_id')->references('id')->on('bill_of_materials')->onDelete('cascade');
        });

        Schema::create('production_work_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('bill_of_materials_id');
            $table->string('work_order_number');
            $table->integer('qty_to_produce');
            $table->bigInteger('total_overhead_cost_cents')->default(0);
            $table->enum('status', ['draft', 'allocated', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'work_order_number']);
            $table->foreign('bill_of_materials_id')->references('id')->on('bill_of_materials')->onDelete('restrict');
        });

        Schema::create('mushak_4_3_declarations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('bill_of_materials_id');
            $table->string('declaration_number');
            $table->bigInteger('declared_cost_base_cents')->default(0);
            $table->bigInteger('declared_overhead_cents')->default(0);
            $table->bigInteger('declared_profit_cents')->default(0);
            $table->bigInteger('declared_sale_price_cents')->default(0);
            $table->date('declared_at');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'declaration_number']);
            $table->foreign('bill_of_materials_id')->references('id')->on('bill_of_materials')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['bill_of_materials', 'bom_items', 'production_work_orders', 'mushak_4_3_declarations'];
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
        Schema::dropIfExists('mushak_4_3_declarations');
        Schema::dropIfExists('production_work_orders');
        Schema::dropIfExists('bom_items');
        Schema::dropIfExists('bill_of_materials');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mrp_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('run_number');
            $table->uuid('initiated_by')->nullable();
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'run_number']);
        });

        Schema::create('mrp_planned_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('mrp_run_id');
            $table->string('item_sku');
            $table->integer('gross_requirement_qty');
            $table->integer('net_requirement_qty');
            $table->integer('safety_stock_threshold')->default(0);
            $table->integer('lead_time_days')->default(0);
            $table->enum('order_recommendation_type', ['purchase_requisition', 'production_work_order']);
            $table->date('planned_order_date');
            $table->date('planned_delivery_date');
            $table->enum('status', ['pending', 'released', 'ignored'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('mrp_run_id')->references('id')->on('mrp_runs')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['mrp_runs', 'mrp_planned_orders'];
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
        Schema::dropIfExists('mrp_planned_orders');
        Schema::dropIfExists('mrp_runs');
    }
};

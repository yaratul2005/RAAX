<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('asset_tag');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('acquisition_date');
            $table->bigInteger('acquisition_cost_cents');
            $table->bigInteger('salvage_value_cents');
            $table->integer('lifespan_months');
            $table->enum('depreciation_method', ['straight_line', 'reducing_balance']);
            $table->integer('depreciation_rate_basis_cents')->nullable(); // e.g., 1500 for 15.00%
            $table->enum('status', ['active', 'fully_depreciated', 'disposed'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'asset_tag']);
        });

        Schema::create('depreciation_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('fixed_asset_id');
            $table->string('period_month'); // YYYY-MM
            $table->bigInteger('depreciation_amount_cents');
            $table->bigInteger('accumulated_depreciation_cents');
            $table->bigInteger('book_value_cents');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'fixed_asset_id', 'period_month']);
            $table->foreign('fixed_asset_id')->references('id')->on('fixed_assets')->onDelete('cascade');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['fixed_assets', 'depreciation_logs'];
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
        Schema::dropIfExists('depreciation_logs');
        Schema::dropIfExists('fixed_assets');
    }
};

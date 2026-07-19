<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasury_deposits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('challan_number');
            $table->date('deposit_date');
            $table->string('bank_branch');
            $table->string('code_of_analysis');
            $table->bigInteger('amount_cents');
            $table->enum('status', ['cleared', 'reversed'])->default('cleared');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'challan_number']);
        });

        Schema::create('mushak_9_1_returns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('tax_period'); // YYYY-MM
            $table->bigInteger('total_sales_value_cents')->default(0);
            $table->bigInteger('total_output_tax_cents')->default(0);
            $table->bigInteger('total_purchases_value_cents')->default(0);
            $table->bigInteger('total_input_tax_cents')->default(0);
            $table->bigInteger('net_tax_payable_cents')->default(0);
            $table->uuid('treasury_deposit_id')->nullable();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'tax_period']);
            $table->foreign('treasury_deposit_id')->references('id')->on('treasury_deposits')->onDelete('restrict');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['treasury_deposits', 'mushak_9_1_returns'];
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
        Schema::dropIfExists('mushak_9_1_returns');
        Schema::dropIfExists('treasury_deposits');
    }
};

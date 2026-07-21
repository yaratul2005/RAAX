<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mushak 4.3: Input-Output Coefficient declaration database
        Schema::create('mushak_4_3s', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->date('declaration_date');
            $table->string('product_name');
            $table->string('hs_code')->nullable();
            $table->char('currency_code', 3);
            $table->timestamps();
            $table->softDeletes();
        });

        // Mushak 6.1: Supplier Purchase Register ledger
        Schema::create('mushak_6_1s', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->date('purchase_date');
            $table->string('supplier_name');
            $table->string('supplier_bin')->nullable();
            $table->string('invoice_number');
            $table->bigInteger('total_amount')->comment('Stored in cents');
            $table->bigInteger('vat_amount')->comment('Stored in cents');
            $table->char('currency_code', 3);
            $table->timestamps();
            $table->softDeletes();
        });

        // Mushak 6.2: Customer Sales Register ledger
        Schema::create('mushak_6_2s', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->date('sales_date');
            $table->string('customer_name');
            $table->string('customer_bin')->nullable();
            $table->string('invoice_number');
            $table->bigInteger('total_amount')->comment('Stored in cents');
            $table->bigInteger('vat_amount')->comment('Stored in cents');
            $table->char('currency_code', 3);
            $table->timestamps();
            $table->softDeletes();
        });

        // Mushak 6.3: Tax Invoice record schema
        Schema::create('mushak_6_3_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->date('issue_date');
            $table->string('buyer_name');
            $table->string('buyer_bin')->nullable();
            $table->bigInteger('total_value')->default(0)->nullable()->comment('Stored in cents');
            $table->bigInteger('total_vat')->default(0)->nullable()->comment('Stored in cents');
            $table->char('currency_code', 3)->default('BDT');
            $table->timestamps();
            $table->softDeletes();
        });

        $tables = ['mushak_4_3s', 'mushak_6_1s', 'mushak_6_2s', 'mushak_6_3_invoices'];

        foreach ($tables as $t) {
            if (config('database.default') !== 'sqlite') {
                DB::statement("ALTER TABLE {$t} ENABLE ROW LEVEL SECURITY;");
            }
            if (config('database.default') !== 'sqlite') {
                DB::statement("ALTER TABLE {$t} FORCE ROW LEVEL SECURITY;");
            }
            if (config('database.default') !== 'sqlite') {
                DB::statement("
                CREATE POLICY tenant_isolation_policy ON {$t}
                FOR ALL
                TO app_user
                USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
            ");
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mushak_6_3_invoices');
        Schema::dropIfExists('mushak_6_2s');
        Schema::dropIfExists('mushak_6_1s');
        Schema::dropIfExists('mushak_4_3s');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->bigInteger('basic_salary_cents');
            $table->bigInteger('house_rent_cents');
            $table->bigInteger('medical_allowance_cents');
            $table->bigInteger('transport_allowance_cents');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'employee_id']);
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        Schema::create('payroll_payslips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->string('billing_month'); // YYYY-MM
            $table->bigInteger('gross_salary_cents');
            $table->integer('unpaid_days')->default(0);
            $table->bigInteger('late_deductions_cents')->default(0);
            $table->bigInteger('withholding_tax_cents')->default(0);
            $table->bigInteger('net_salary_cents');
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'employee_id', 'billing_month']);
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
        });

        if (config('database.default') !== 'sqlite') {
            $tables = ['employee_salaries', 'payroll_payslips'];
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
        Schema::dropIfExists('payroll_payslips');
        Schema::dropIfExists('employee_salaries');
    }
};

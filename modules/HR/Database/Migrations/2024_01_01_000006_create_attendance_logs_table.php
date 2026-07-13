<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->uuid('shift_id');
            $table->date('date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->integer('worked_minutes')->default(0);
            $table->enum('status', ['Present', 'Late', 'Absent', 'Half-Day'])->default('Present');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('restrict');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('restrict');
        });

        if (config('database.default') !== 'sqlite') {
            DB::statement('ALTER TABLE attendance_logs ENABLE ROW LEVEL SECURITY;');
            DB::statement('ALTER TABLE attendance_logs FORCE ROW LEVEL SECURITY;');
            DB::statement("
                CREATE POLICY tenant_isolation_policy ON attendance_logs
                FOR ALL
                TO app_user
                USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};

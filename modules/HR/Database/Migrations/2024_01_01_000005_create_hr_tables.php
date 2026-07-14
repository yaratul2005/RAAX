<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('title');
            $table->integer('grade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('gender')->default('male');
            $table->string('phone')->nullable();
            $table->uuid('department_id');
            $table->uuid('designation_id');
            $table->date('joining_date');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'email']);

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('restrict');
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('grace_period_minutes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $tables = ['departments', 'designations', 'employees', 'shifts'];

        if (config('database.default') !== 'sqlite') {
            foreach ($tables as $t) {
                DB::statement("ALTER TABLE {$t} ENABLE ROW LEVEL SECURITY;");
                DB::statement("ALTER TABLE {$t} FORCE ROW LEVEL SECURITY;");
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
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
};

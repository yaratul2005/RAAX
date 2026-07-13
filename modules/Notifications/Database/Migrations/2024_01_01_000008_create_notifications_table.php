<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            // Using UUID for users as per project pattern, even if default Laravel is bigInt, but wait, role_user has user_id foreignId. Let's stick to string/uuid
            $table->string('user_id')->nullable();
            $table->string('type'); // email, sms
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (config('database.default') !== 'sqlite') {
            DB::statement('ALTER TABLE notifications ENABLE ROW LEVEL SECURITY;');
            DB::statement('ALTER TABLE notifications FORCE ROW LEVEL SECURITY;');
            DB::statement("
                CREATE POLICY tenant_isolation_policy ON notifications
                FOR ALL
                TO app_user
                USING (tenant_id = NULLIF(current_setting('app.current_tenant_id', TRUE), '')::UUID);
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

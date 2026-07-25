<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_sequences', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->uuid('tenant_id');
            $table->bigInteger('counter')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_sequences');
    }
};

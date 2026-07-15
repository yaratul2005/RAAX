<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mushak_6_3_invoices')) {
            Schema::table('mushak_6_3_invoices', function (Blueprint $table) {
                // Add any missing columns based on our model
                if (!Schema::hasColumn('mushak_6_3_invoices', 'sales_order_id')) {
                    $table->uuid('sales_order_id')->nullable();
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'challan_number')) {
                    $table->string('challan_number')->nullable();
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'buyer_name')) {
                    $table->string('buyer_name')->nullable();
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'buyer_bin')) {
                    $table->string('buyer_bin')->nullable();
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'seller_name')) {
                    $table->string('seller_name')->nullable();
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'seller_bin')) {
                    $table->string('seller_bin')->nullable();
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'subtotal_cents')) {
                    $table->bigInteger('subtotal_cents')->default(0);
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'vat_cents')) {
                    $table->bigInteger('vat_cents')->default(0);
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'total_payable_cents')) {
                    $table->bigInteger('total_payable_cents')->default(0);
                }
                if (!Schema::hasColumn('mushak_6_3_invoices', 'is_high_value_audit')) {
                    $table->boolean('is_high_value_audit')->default(false);
                }
            });
        }
    }

    public function down(): void
    {
        // Not dropping columns in down() to avoid data loss on rollback for MVP
    }
};

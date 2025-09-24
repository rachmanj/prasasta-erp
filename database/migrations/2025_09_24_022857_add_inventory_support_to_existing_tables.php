<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add inventory support to Purchase Order Lines
        Schema::table('purchase_order_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('account_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->index('item_id');
        });

        // Add inventory support to Sales Order Lines
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('account_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->index('item_id');
        });

        // Add inventory support to Goods Receipt Lines
        Schema::table('goods_receipt_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('account_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->index('item_id');
        });

        // Add inventory support to Purchase Invoice Lines
        Schema::table('purchase_invoice_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('account_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->index('item_id');
        });

        // Add inventory support to Sales Invoice Lines
        Schema::table('sales_invoice_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('account_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->index('item_id');
        });

        // Add inventory support to Purchase Payments (for tracking payments against inventory purchases)
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->boolean('affects_inventory')->default(false)->after('status');
            $table->index('affects_inventory');
        });

        // Add inventory support to Sales Receipts (for tracking receipts from inventory sales)
        Schema::table('sales_receipts', function (Blueprint $table) {
            $table->boolean('affects_inventory')->default(false)->after('status');
            $table->index('affects_inventory');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove inventory support from Purchase Payments
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->dropIndex(['affects_inventory']);
            $table->dropColumn('affects_inventory');
        });

        // Remove inventory support from Sales Receipts
        Schema::table('sales_receipts', function (Blueprint $table) {
            $table->dropIndex(['affects_inventory']);
            $table->dropColumn('affects_inventory');
        });

        // Remove inventory support from Sales Invoice Lines
        Schema::table('sales_invoice_lines', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropIndex(['item_id']);
            $table->dropColumn('item_id');
        });

        // Remove inventory support from Purchase Invoice Lines
        Schema::table('purchase_invoice_lines', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropIndex(['item_id']);
            $table->dropColumn('item_id');
        });

        // Remove inventory support from Goods Receipt Lines
        Schema::table('goods_receipt_lines', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropIndex(['item_id']);
            $table->dropColumn('item_id');
        });

        // Remove inventory support from Sales Order Lines
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropIndex(['item_id']);
            $table->dropColumn('item_id');
        });

        // Remove inventory support from Purchase Order Lines
        Schema::table('purchase_order_lines', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropIndex(['item_id']);
            $table->dropColumn('item_id');
        });
    }
};

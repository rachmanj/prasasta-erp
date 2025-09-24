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
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_account_id')->nullable()->after('category_id');
            $table->unsignedBigInteger('cost_of_goods_sold_account_id')->nullable()->after('inventory_account_id');

            $table->foreign('inventory_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('cost_of_goods_sold_account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['inventory_account_id']);
            $table->dropForeign(['cost_of_goods_sold_account_id']);
            $table->dropColumn(['inventory_account_id', 'cost_of_goods_sold_account_id']);
        });
    }
};

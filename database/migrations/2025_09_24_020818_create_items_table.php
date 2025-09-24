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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->foreignId('category_id')->constrained('inventory_categories')->onDelete('restrict');
            $table->unsignedBigInteger('inventory_account_id')->nullable()->after('category_id');
            $table->unsignedBigInteger('cost_of_goods_sold_account_id')->nullable()->after('inventory_account_id');
            $table->enum('type', ['item', 'service'])->default('item');
            $table->string('unit_of_measure')->default('pcs');
            $table->enum('cost_method', ['fifo'])->default('fifo');
            $table->decimal('min_stock_level', 15, 4)->default(0);
            $table->decimal('max_stock_level', 15, 4)->nullable();
            $table->decimal('current_stock_quantity', 15, 4)->default(0);
            $table->decimal('current_stock_value', 15, 2)->default(0);
            $table->decimal('last_cost_price', 15, 2)->nullable();
            $table->decimal('average_cost_price', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('inventory_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->foreign('cost_of_goods_sold_account_id')->references('id')->on('accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};

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
            // Add indexes for search optimization
            $table->index(['is_active', 'type'], 'idx_items_active_type');
            $table->index(['is_active', 'category_id'], 'idx_items_active_category');
            $table->index(['code'], 'idx_items_code_search');
            $table->index(['name'], 'idx_items_name_search');
            $table->index(['barcode'], 'idx_items_barcode_search');
            $table->index(['updated_at'], 'idx_items_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('idx_items_active_type');
            $table->dropIndex('idx_items_active_category');
            $table->dropIndex('idx_items_code_search');
            $table->dropIndex('idx_items_name_search');
            $table->dropIndex('idx_items_barcode_search');
            $table->dropIndex('idx_items_updated_at');
        });
    }
};

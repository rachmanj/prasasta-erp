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
        Schema::create('user_item_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->integer('usage_count')->default(1);
            $table->timestamp('last_used_at');
            $table->timestamps();

            // Unique constraint to prevent duplicate preferences per user-item combination
            $table->unique(['user_id', 'item_id']);

            // Indexes for performance
            $table->index(['user_id', 'last_used_at']);
            $table->index(['user_id', 'usage_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_item_preferences');
    }
};

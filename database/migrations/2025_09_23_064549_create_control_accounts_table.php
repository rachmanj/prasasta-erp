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
        Schema::create('control_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., '1.1.4', '2.1.1'
            $table->string('name'); // e.g., 'Accounts Receivable', 'Accounts Payable'
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('control_type', ['ar', 'ap', 'inventory', 'fixed_assets', 'cash', 'other']);
            $table->boolean('is_active')->default(true);
            $table->enum('reconciliation_frequency', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->decimal('tolerance_amount', 15, 2)->default(0.00); // for reconciliation variance
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['type', 'control_type']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_accounts');
    }
};

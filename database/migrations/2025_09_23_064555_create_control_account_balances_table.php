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
        Schema::create('control_account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_account_id')->constrained('control_accounts')->onDelete('cascade');
            $table->string('period', 7); // YYYY-MM format
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('total_debits', 15, 2)->default(0.00);
            $table->decimal('total_credits', 15, 2)->default(0.00);
            $table->decimal('calculated_balance', 15, 2)->default(0.00);
            $table->decimal('subsidiary_total', 15, 2)->default(0.00); // sum of subsidiary balances
            $table->decimal('variance_amount', 15, 2)->default(0.00); // calculated_balance - subsidiary_total
            $table->enum('reconciliation_status', ['pending', 'reconciled', 'variance'])->default('pending');
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Unique constraint: one balance record per control account per period
            $table->unique(['control_account_id', 'period'], 'control_balance_unique');

            // Indexes for performance
            $table->index(['control_account_id', 'period'], 'control_balance_period_idx');
            $table->index('reconciliation_status', 'reconciliation_status_idx');
            $table->index('reconciled_at', 'reconciled_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_account_balances');
    }
};

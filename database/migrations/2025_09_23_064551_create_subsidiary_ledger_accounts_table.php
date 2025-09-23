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
        Schema::create('subsidiary_ledger_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('control_account_id')->constrained('control_accounts')->onDelete('cascade');
            $table->string('subsidiary_code'); // unique within control account
            $table->string('name'); // e.g., 'PT Maju Bersama', 'Office Supplies - Jakarta'
            $table->enum('subsidiary_type', ['customer', 'vendor', 'location', 'category', 'other']);
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('current_balance', 15, 2)->default(0.00); // calculated field
            $table->date('last_transaction_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // for additional attributes
            $table->timestamps();

            // Unique constraint: subsidiary_code must be unique within each control account
            $table->unique(['control_account_id', 'subsidiary_code'], 'subsidiary_unique_code');

            // Indexes for performance
            $table->index(['control_account_id', 'subsidiary_type'], 'subsidiary_type_idx');
            $table->index('is_active', 'subsidiary_active_idx');
            $table->index('last_transaction_date', 'subsidiary_transaction_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subsidiary_ledger_accounts');
    }
};

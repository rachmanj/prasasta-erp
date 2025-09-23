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
        Schema::create('cash_outs', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique();
            $table->date('date');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('cash_account_id'); // Single cash/bank account (credit)
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('posted'); // posted, reversed
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('fund_id')->nullable();
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->timestamps();

            $table->foreign('cash_account_id')->references('id')->on('accounts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('fund_id')->references('id')->on('funds');
            $table->foreign('dept_id')->references('id')->on('departments');

            $table->index(['date', 'status']);
            $table->index('voucher_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_outs');
    }
};

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
        Schema::create('cash_out_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cash_out_id');
            $table->unsignedBigInteger('account_id'); // Debit account (expense, asset, etc.)
            $table->decimal('amount', 15, 2);
            $table->text('memo')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('fund_id')->nullable();
            $table->unsignedBigInteger('dept_id')->nullable();
            $table->timestamps();

            $table->foreign('cash_out_id')->references('id')->on('cash_outs')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('fund_id')->references('id')->on('funds');
            $table->foreign('dept_id')->references('id')->on('departments');

            $table->index(['cash_out_id', 'account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_out_lines');
    }
};

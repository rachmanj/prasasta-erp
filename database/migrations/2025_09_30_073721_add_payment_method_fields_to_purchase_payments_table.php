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
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'other'])->default('bank_transfer')->after('description');
            $table->string('check_number')->nullable()->after('payment_method');
            $table->string('reference_number')->nullable()->after('check_number');
            $table->unsignedBigInteger('bank_account_id')->nullable()->after('reference_number');

            $table->foreign('bank_account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->index(['payment_method', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropIndex(['payment_method', 'date']);
            $table->dropColumn(['payment_method', 'check_number', 'reference_number', 'bank_account_id']);
        });
    }
};

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
        Schema::table('installment_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('journal_entry_id')->nullable()->after('reference_number');
            $table->boolean('is_accounted_for')->default(false)->after('journal_entry_id');
            $table->timestamp('accounted_at')->nullable()->after('is_accounted_for');

            $table->foreign('journal_entry_id')->references('id')->on('journals')->nullOnDelete();
            $table->index(['is_accounted_for', 'accounted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installment_payments', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
            $table->dropIndex(['is_accounted_for', 'accounted_at']);
            $table->dropColumn(['journal_entry_id', 'is_accounted_for', 'accounted_at']);
        });
    }
};

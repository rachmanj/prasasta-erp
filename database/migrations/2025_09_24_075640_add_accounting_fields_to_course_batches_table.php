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
        Schema::table('course_batches', function (Blueprint $table) {
            $table->boolean('revenue_recognized')->default(false);
            $table->timestamp('revenue_recognized_at')->nullable();
            $table->unsignedBigInteger('revenue_recognition_journal_id')->nullable();

            $table->foreign('revenue_recognition_journal_id')->references('id')->on('journals')->nullOnDelete();
            $table->index(['revenue_recognized', 'revenue_recognized_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_batches', function (Blueprint $table) {
            $table->dropForeign(['revenue_recognition_journal_id']);
            $table->dropIndex(['revenue_recognized', 'revenue_recognized_at']);
            $table->dropColumn(['revenue_recognized', 'revenue_recognized_at', 'revenue_recognition_journal_id']);
        });
    }
};

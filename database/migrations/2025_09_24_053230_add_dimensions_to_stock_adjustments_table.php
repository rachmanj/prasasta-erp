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
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('approved_at');
            $table->unsignedBigInteger('fund_id')->nullable()->after('project_id');
            $table->unsignedBigInteger('dept_id')->nullable()->after('fund_id');

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('fund_id')->references('id')->on('funds')->onDelete('set null');
            $table->foreign('dept_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['fund_id']);
            $table->dropForeign(['dept_id']);

            $table->dropColumn(['project_id', 'fund_id', 'dept_id']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_expenses', function (Blueprint $table) {
            $table->string('voucher_number')->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('cash_expenses', function (Blueprint $table) {
            $table->dropColumn('voucher_number');
        });
    }
};

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
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->enum('type', ['internal', 'external'])->default('internal');
            $table->string('specialization', 255)->nullable();
            $table->text('qualifications')->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('batch_rate', 10, 2)->nullable();
            $table->decimal('revenue_share_percentage', 5, 2)->nullable();
            $table->string('bank_account', 255)->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};

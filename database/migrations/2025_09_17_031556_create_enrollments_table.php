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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // References customers table
            $table->unsignedBigInteger('batch_id');
            $table->date('enrollment_date');
            $table->enum('status', ['enrolled', 'completed', 'dropped', 'suspended'])->default('enrolled');
            $table->unsignedBigInteger('payment_plan_id')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('customers');
            $table->foreign('batch_id')->references('id')->on('course_batches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};

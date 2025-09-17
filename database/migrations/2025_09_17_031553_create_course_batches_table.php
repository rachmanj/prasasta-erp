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
        Schema::create('course_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('batch_code', 50);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('schedule')->nullable(); // days, times, locations
            $table->string('location', 255)->nullable();
            $table->unsignedBigInteger('trainer_id')->nullable(); // Will link to trainers later
            $table->integer('capacity');
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled'])->default('planned');
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses');
            $table->unique(['course_id', 'batch_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_batches');
    }
};

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
        Schema::create('revenue_recognitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('batch_id');
            $table->date('recognition_date');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['deferred', 'recognized', 'reversed'])->default('deferred');
            $table->text('description')->nullable();
            $table->string('journal_entry_id', 50)->nullable();
            $table->boolean('is_posted')->default(false);
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments');
            $table->foreign('batch_id')->references('id')->on('course_batches');
            $table->index(['recognition_date', 'type']);
            $table->index(['batch_id', 'recognition_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_recognitions');
    }
};

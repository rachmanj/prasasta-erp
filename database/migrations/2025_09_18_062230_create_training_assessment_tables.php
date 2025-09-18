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
        // Training Questions Table
        Schema::create('training_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->enum('type', ['multiple_choice', 'calculation', 'scenario']);
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->string('category');
            $table->json('options')->nullable();
            $table->string('correct_answer');
            $table->text('explanation');
            $table->timestamps();
        });

        // Training Scenarios Table
        Schema::create('training_scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->string('category');
            $table->json('steps');
            $table->json('expected_outcomes');
            $table->timestamps();
        });

        // Training Answers Table
        Schema::create('training_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('training_questions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('answer');
            $table->boolean('is_correct');
            $table->timestamp('answered_at');
            $table->timestamps();

            $table->unique(['question_id', 'user_id']);
        });

        // Training Assessments Table
        Schema::create('training_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['knowledge', 'practical', 'scenario']);
            $table->integer('score');
            $table->integer('total_questions');
            $table->decimal('percentage', 5, 2);
            $table->enum('status', ['passed', 'failed', 'in_progress']);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Training Certifications Table
        Schema::create('training_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('level', ['basic', 'advanced', 'expert']);
            $table->date('issued_date');
            $table->date('expiry_date');
            $table->string('certificate_number')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_certifications');
        Schema::dropIfExists('training_assessments');
        Schema::dropIfExists('training_answers');
        Schema::dropIfExists('training_scenarios');
        Schema::dropIfExists('training_questions');
    }
};

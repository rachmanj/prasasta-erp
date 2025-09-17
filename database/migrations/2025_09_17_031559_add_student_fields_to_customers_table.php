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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('student_id', 50)->unique()->nullable();
            $table->string('emergency_contact_name', 255)->nullable();
            $table->string('emergency_contact_phone', 50)->nullable();
            $table->enum('student_status', ['active', 'graduated', 'suspended'])->default('active');
            $table->integer('enrollment_count')->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'emergency_contact_name',
                'emergency_contact_phone',
                'student_status',
                'enrollment_count',
                'total_paid'
            ]);
        });
    }
};

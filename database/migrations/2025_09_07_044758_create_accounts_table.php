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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->enum('type', ['asset', 'liability', 'net_assets', 'income', 'expense']);
            $table->enum('control_type', ['ap', 'ar', 'cash', 'inventory', 'fixed_assets', 'other'])->nullable()->after('type');
            $table->boolean('is_control_account')->default(false)->after('control_type');
            $table->text('description')->nullable()->after('is_control_account');
            $table->enum('reconciliation_frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly')->after('description');
            $table->decimal('tolerance_amount', 15, 2)->default(0.00)->after('reconciliation_frequency');
            $table->boolean('is_postable')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

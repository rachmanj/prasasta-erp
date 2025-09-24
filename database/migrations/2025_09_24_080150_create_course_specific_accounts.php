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
        // Add course-specific accounts for better categorization
        DB::table('accounts')->insert([
            [
                'code' => '2.1.5.1',
                'name' => 'Deferred Revenue - Digital Marketing',
                'type' => 'liability',
                'is_control_account' => false,
                'is_postable' => true,
                'parent_id' => DB::table('accounts')->where('code', '2.1.5')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '2.1.5.2',
                'name' => 'Deferred Revenue - Data Analytics',
                'type' => 'liability',
                'is_control_account' => false,
                'is_postable' => true,
                'parent_id' => DB::table('accounts')->where('code', '2.1.5')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '2.1.5.3',
                'name' => 'Deferred Revenue - Project Management',
                'type' => 'liability',
                'is_control_account' => false,
                'is_postable' => true,
                'parent_id' => DB::table('accounts')->where('code', '2.1.5')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '4.1.1.1',
                'name' => 'Course Revenue - Digital Marketing',
                'type' => 'income',
                'is_control_account' => false,
                'is_postable' => true,
                'parent_id' => DB::table('accounts')->where('code', '4.1.1')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '4.1.1.2',
                'name' => 'Course Revenue - Data Analytics',
                'type' => 'income',
                'is_control_account' => false,
                'is_postable' => true,
                'parent_id' => DB::table('accounts')->where('code', '4.1.1')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '4.1.1.3',
                'name' => 'Course Revenue - Project Management',
                'type' => 'income',
                'is_control_account' => false,
                'is_postable' => true,
                'parent_id' => DB::table('accounts')->where('code', '4.1.1')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '4.1.3',
                'name' => 'Course Cancellation Revenue',
                'type' => 'income',
                'is_control_account' => false,
                'is_postable' => true,
                'parent_id' => DB::table('accounts')->where('code', '4.1')->value('id'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('accounts')->whereIn('code', [
            '2.1.5.1',
            '2.1.5.2',
            '2.1.5.3',
            '4.1.1.1',
            '4.1.1.2',
            '4.1.1.3',
            '4.1.3'
        ])->delete();
    }
};

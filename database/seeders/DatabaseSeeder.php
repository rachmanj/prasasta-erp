<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CoASeeder::class,
            TaxCodeSeeder::class,
            FundProjectSeeder::class,
            RolePermissionSeeder::class,
            SampleUsersSeeder::class,
            TermsSeeder::class,
            TrainingDataSeeder::class,
            TrainingScenariosSeeder::class,
            TrainingAssessmentSeeder::class,
        ]);
    }
}

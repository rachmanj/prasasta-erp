<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi.accountant',
                'email' => 'budi@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'accountant',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'username' => 'siti.approver',
                'email' => 'siti@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'approver',
            ],
            [
                'name' => 'Ahmad Wijaya',
                'username' => 'ahmad.cashier',
                'email' => 'ahmad@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ],
            [
                'name' => 'Maria Magdalena',
                'username' => 'maria.auditor',
                'email' => 'maria@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'auditor',
            ],
            [
                'name' => 'Rina Sari',
                'username' => 'rina.accountant2',
                'email' => 'rina@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'accountant',
            ],
            [
                'name' => 'Joko Widodo',
                'username' => 'joko.approver2',
                'email' => 'joko@prasasta.com',
                'password' => Hash::make('password'),
                'role' => 'approver',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);
        }
    }
}

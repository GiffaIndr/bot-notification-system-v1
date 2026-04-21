<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'tugas.tasku@gmail.com'],
            [
                'name'              => 'Super Admin - Pemilik App',
                'email'             => 'tugas.tasku@gmail.com',
                'password'          => Hash::make('SuperAdmin@2024!Secure'),
                'phone'             => '+62-XXX-XXXX',
                'email_verified_at' => now(),
                'is_super_admin'    => true,
            ]
        );

        $this->command->info('Super Admin akun berhasil dibuat/diupdate.');
        $this->command->info('Email: tugas.tasku@gmail.com');
        $this->command->info('Password: SuperAdmin@2024!Secure (ubah segera setelah login pertama)');
    }
}

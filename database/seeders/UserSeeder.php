<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'User One',   'email' => 'user1@gmail.com'],
            ['name' => 'User Two',   'email' => 'user2@gmail.com'],
            ['name' => 'User Three', 'email' => 'user3@gmail.com'],
        ];

        foreach ($users as $data) {
            User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('user123'),
            ]);
        }
    }
}

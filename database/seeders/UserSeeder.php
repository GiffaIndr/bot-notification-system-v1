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
            ['name' => 'Giffa',   'email' => 'user1@gmail.com', 'phone' => '6287781045812'],
            ['name' => 'Husnul',   'email' => 'user2@gmail.com', 'phone' => '6285783627927'],
            ['name' => 'Salsa', 'email' => 'user3@gmail.com', 'phone' => '6282181942593'],
            ['name' => 'syabib', 'email' => 'user4@gmail.com', 'phone' => '6282115314179'],
            ['name' => 'misael', 'email' => 'user5@gmail.com', 'phone' => '6281384700455'],
            ['name' => 'Acul', 'email' => 'user6@gmail.com', 'phone' => '6281258234515'],
        ];

        foreach ($users as $data) {
            User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make('user123'),
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::create([
            'name' => 'Basic',
            'price' => 15000,
            'description' => 'Cocok untuk pemula. Notifikasi via WhatsApp untuk 1 group.',
            'whatsapp' => true,
            'discord'  => false,
            'telegram' => false,
            'max_group' => 1,
        ]);

        Plan::create([
            'name' => 'Pro',
            'price'  => 30000,
            'description' => 'Untuk yang lebih aktif. Notifikasi via WhatsApp, Discord & Telegram untuk 2 group.',
            'whatsapp' => true,
            'discord' => true,
            'telegram' => true,
            'max_group' => 2,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'price'  => 50000,
            'description' => 'Untuk organisasi besar. Semua fitur lengkap dengan maksimal 5 group.',
            'whatsapp' => true,
            'discord' => true,
            'telegram' => true,
            'max_group' => 5,
        ]);
    }
}

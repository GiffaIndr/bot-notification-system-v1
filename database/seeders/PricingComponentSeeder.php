<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PricingComponent;

class PricingComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            [
                'key'         => 'whatsapp',
                'name'        => 'WhatsApp Bot',
                'price'       => 10000,
                'description' => 'Notifikasi via WhatsApp ke semua member',
            ],
            [
                'key'         => 'discord',
                'name'        => 'Discord Bot',
                'price'       => 8000,
                'description' => 'Notifikasi via Discord ke channel pilihan',
            ],
            [
                'key'         => 'telegram',
                'name'        => 'Telegram Bot',
                'price'       => 8000,
                'description' => 'Notifikasi via Telegram ke group pilihan',
            ],
            [
                'key'         => 'per_group',
                'name'        => 'Per Group',
                'price'       => 15000,
                'description' => 'Harga per group yang dibuat',
            ],
            [
                'key'         => 'per_member',
                'name'        => 'Per Member',
                'price'       => 5000,
                'description' => 'Harga per member dalam group',
            ],
        ];

        foreach ($components as $component) {
            PricingComponent::updateOrCreate(['key' => $component['key']], $component);
        }
    }
}

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
                'key'         => 'base_plan',
                'name'        => 'Harga Dasar (10 Member)',
                'price'       => 15000,
                'description' => 'Harga dasar untuk group dengan 10 anggota',
            ],
            [
                'key'         => 'additional_members',
                'name'        => 'Tambahan Kapasitas (per 5 Member)',
                'price'       => 5000,
                'description' => 'Tambahan per 5 anggota di atas 10 member base',
            ],
            [
                'key'         => 'whatsapp',
                'name'        => 'Integrasi Bot WhatsApp',
                'price'       => 15000,
                'description' => 'Notifikasi via WhatsApp ke semua member',
            ],
            [
                'key'         => 'discord',
                'name'        => 'Integrasi Bot Discord',
                'price'       => 10000,
                'description' => 'Notifikasi via Discord ke channel pilihan',
            ],
            [
                'key'         => 'telegram',
                'name'        => 'Integrasi Bot Telegram',
                'price'       => 10000,
                'description' => 'Notifikasi via Telegram ke group pilihan',
            ],
        ];

        foreach ($components as $component) {
            PricingComponent::updateOrCreate(['key' => $component['key']], $component);
        }
    }
}

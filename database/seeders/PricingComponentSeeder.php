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
            // Upgrade pricing components
            [
                'key'         => 'upgrade_extend_month',
                'name'        => 'Perpanjangan Langganan (Per Bulan)',
                'price'       => 10000,
                'description' => 'Harga untuk perpanjangan langganan group per 1 bulan',
            ],
            [
                'key'         => 'upgrade_member_slot',
                'name'        => 'Slot Tambahan Member (Per 5 Orang)',
                'price'       => 5000,
                'description' => 'Harga untuk menambah kapasitas member sebanyak 5 orang',
            ],
            [
                'key'         => 'upgrade_bot',
                'name'        => 'Tambahan Bot Integrasi',
                'price'       => 75000,
                'description' => 'Harga untuk menambah 1 bot integrasi (Discord/Telegram/WhatsApp)',
            ],
        ];

        foreach ($components as $component) {
            PricingComponent::updateOrCreate(['key' => $component['key']], $component);
        }
    }
}

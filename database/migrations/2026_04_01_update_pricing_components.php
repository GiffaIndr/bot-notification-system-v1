<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update pricing components untuk mencapai total Rp 30.000 per bulan
        // Perhitungan: (20k + 20k + 20k + 40k + 10 × 8k) / 6 = 180k / 6 = 30k/bulan
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'whatsapp'],
            ['price' => 20000, 'name' => 'WhatsApp Bot']
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'discord'],
            ['price' => 20000, 'name' => 'Discord Bot']
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'telegram'],
            ['price' => 20000, 'name' => 'Telegram Bot']
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'per_group'],
            ['price' => 40000, 'name' => 'Per Group']
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'per_member'],
            ['price' => 8000, 'name' => 'Per Member']
        );
    }

    public function down(): void
    {
        // Rollback ke harga lama
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'whatsapp'],
            ['price' => 10000]
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'discord'],
            ['price' => 8000]
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'telegram'],
            ['price' => 8000]
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'per_group'],
            ['price' => 15000]
        );
        
        DB::table('pricing_components')->updateOrInsert(
            ['key' => 'per_member'],
            ['price' => 5000]
        );
    }
};

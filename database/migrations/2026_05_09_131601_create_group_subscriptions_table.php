<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->unique()->constrained('groups')->onDelete('cascade');
            $table->integer('max_members')->default(50);
            $table->integer('active_bots_count')->default(0); // Berapa bots yang sudah diaktifkan
            $table->integer('max_bots')->default(1); // Max bots yang bisa diaktifkan (1=Discord, 2=+Telegram, 3=+WhatsApp)
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_subscriptions');
    }
};

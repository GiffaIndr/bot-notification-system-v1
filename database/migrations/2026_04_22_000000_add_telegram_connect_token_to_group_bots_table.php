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
        Schema::table('group_bots', function (Blueprint $table) {
            if (!Schema::hasColumn('group_bots', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable()->after('discord_channel_id');
            }

            if (!Schema::hasColumn('group_bots', 'telegram_connect_token')) {
                $table->string('telegram_connect_token', 64)->nullable()->unique()->after('telegram_chat_id');
            }

            if (!Schema::hasColumn('group_bots', 'telegram_connect_token_generated_at')) {
                $table->timestamp('telegram_connect_token_generated_at')->nullable()->after('telegram_connect_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_bots', function (Blueprint $table) {
            if (Schema::hasColumn('group_bots', 'telegram_connect_token_generated_at')) {
                $table->dropColumn('telegram_connect_token_generated_at');
            }

            if (Schema::hasColumn('group_bots', 'telegram_connect_token')) {
                $table->dropUnique(['telegram_connect_token']);
                $table->dropColumn('telegram_connect_token');
            }

            if (Schema::hasColumn('group_bots', 'telegram_chat_id')) {
                $table->dropColumn('telegram_chat_id');
            }
        });
    }
};

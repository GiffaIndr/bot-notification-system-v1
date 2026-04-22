<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_bots', function (Blueprint $table) {
            if (!Schema::hasColumn('group_bots', 'discord_guild_id')) {
                $table->string('discord_guild_id')->nullable()->after('discord_channel_id');
            }

            if (!Schema::hasColumn('group_bots', 'discord_server_name')) {
                $table->string('discord_server_name')->nullable()->after('discord_guild_id');
            }

            if (!Schema::hasColumn('group_bots', 'discord_channel_name')) {
                $table->string('discord_channel_name')->nullable()->after('discord_server_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_bots', function (Blueprint $table) {
            if (Schema::hasColumn('group_bots', 'discord_channel_name')) {
                $table->dropColumn('discord_channel_name');
            }

            if (Schema::hasColumn('group_bots', 'discord_server_name')) {
                $table->dropColumn('discord_server_name');
            }

            if (Schema::hasColumn('group_bots', 'discord_guild_id')) {
                $table->dropColumn('discord_guild_id');
            }
        });
    }
};
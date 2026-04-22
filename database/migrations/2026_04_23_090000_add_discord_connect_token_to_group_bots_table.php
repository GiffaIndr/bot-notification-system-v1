<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_bots', function (Blueprint $table) {
            if (!Schema::hasColumn('group_bots', 'discord_connect_token')) {
                $table->string('discord_connect_token', 128)->nullable()->unique()->after('discord_channel_name');
            }

            if (!Schema::hasColumn('group_bots', 'discord_connect_state')) {
                $table->string('discord_connect_state', 255)->nullable()->after('discord_connect_token');
            }

            if (!Schema::hasColumn('group_bots', 'discord_connect_token_generated_at')) {
                $table->timestamp('discord_connect_token_generated_at')->nullable()->after('discord_connect_state');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_bots', function (Blueprint $table) {
            if (Schema::hasColumn('group_bots', 'discord_connect_token_generated_at')) {
                $table->dropColumn('discord_connect_token_generated_at');
            }

            if (Schema::hasColumn('group_bots', 'discord_connect_state')) {
                $table->dropColumn('discord_connect_state');
            }

            if (Schema::hasColumn('group_bots', 'discord_connect_token')) {
                $table->dropUnique(['discord_connect_token']);
                $table->dropColumn('discord_connect_token');
            }
        });
    }
};

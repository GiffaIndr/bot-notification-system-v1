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
        Schema::table('group_members', function (Blueprint $table) {
            $table->index(['user_id', 'role_id'], 'group_members_user_role_idx');
            $table->index(['group_id', 'user_id'], 'group_members_group_user_idx');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->index(['group_id', 'created_at'], 'announcements_group_created_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('order_id', 'payments_order_id_idx');
            $table->index(['user_id', 'created_at'], 'payments_user_created_idx');
            $table->index(['status', 'created_at'], 'payments_status_created_idx');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['user_id', 'expires_at'], 'subscriptions_user_expires_idx');
            $table->index(['user_id', 'starts_at'], 'subscriptions_user_starts_idx');
        });

        Schema::table('group_bots', function (Blueprint $table) {
            $table->index(['group_id', 'type'], 'group_bots_group_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_bots', function (Blueprint $table) {
            $table->dropIndex('group_bots_group_type_idx');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_user_starts_idx');
            $table->dropIndex('subscriptions_user_expires_idx');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_created_idx');
            $table->dropIndex('payments_user_created_idx');
            $table->dropIndex('payments_order_id_idx');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('announcements_group_created_idx');
        });

        Schema::table('group_members', function (Blueprint $table) {
            $table->dropIndex('group_members_group_user_idx');
            $table->dropIndex('group_members_user_role_idx');
        });
    }
};

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
        // Skip if subscription_id already exists
        if (Schema::hasColumn('payments', 'subscription_id')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('user_id')->constrained('subscriptions')->nullOnDelete();
        });

        // Drop old plan_id column if exists
        if (Schema::hasColumn('payments', 'plan_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['plan_id']);
                $table->dropColumn('plan_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('payments', 'subscription_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['subscription_id']);
                $table->dropColumn('subscription_id');
            });
        }
    }
};

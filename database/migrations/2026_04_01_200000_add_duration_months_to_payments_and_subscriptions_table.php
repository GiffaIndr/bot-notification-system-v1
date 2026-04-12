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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedTinyInteger('duration_months')->default(6)->after('total_price');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedTinyInteger('duration_months')->default(6)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('duration_months');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('duration_months');
        });
    }
};

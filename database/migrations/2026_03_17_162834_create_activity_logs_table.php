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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // create_announcement, edit_announcement, delete_announcement, bot_connected, notification_sent, dll
            $table->string('description');
            $table->json('meta')->nullable(); // data tambahan, misal announcement title, bot type, dll
            $table->enum('status', ['success', 'failed', 'pending'])->default('success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('activity_logs');
        Schema::enableForeignKeyConstraints();
    }
};

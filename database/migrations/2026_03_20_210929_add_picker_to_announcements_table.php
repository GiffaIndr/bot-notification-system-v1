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
        Schema::table('announcements', function (Blueprint $table) {
            $table->boolean('use_picker')->default(false)->after('repeat');
            $table->enum('picker_mode', ['members', 'custom'])->default('members')->after('use_picker');
            $table->integer('pick_count')->default(1)->after('picker_mode');
            $table->foreignId('pick_role_id')->nullable()->after('pick_count')->constrained('group_roles')->nullOnDelete();
            $table->text('custom_pick_list')->nullable()->after('pick_role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            //
        });
    }
};

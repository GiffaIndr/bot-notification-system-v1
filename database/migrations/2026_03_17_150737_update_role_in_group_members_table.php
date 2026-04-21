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
            $table->foreignId('role_id')->nullable()->after('user_id')->constrained('group_roles')->nullOnDelete();
            $table->dropColumn('role'); // hapus kolom role lama
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('group_members', function (Blueprint $table) {
            $table->string('role')->default('member');
            if (Schema::hasColumn('group_members', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
        });
        Schema::enableForeignKeyConstraints();
    }
};

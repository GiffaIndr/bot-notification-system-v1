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
        Schema::create('group_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color')->default('#6c757d');
            $table->boolean('can_create_announcement')->default(false);
            $table->boolean('can_edit_announcement')->default(false);
            $table->boolean('can_manage_member')->default(false);
            $table->boolean('can_generate_code')->default(false);
            $table->boolean('can_manage_bot')->default(false);
            $table->boolean('is_owner')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('group_roles');
        Schema::enableForeignKeyConstraints();
    }
};

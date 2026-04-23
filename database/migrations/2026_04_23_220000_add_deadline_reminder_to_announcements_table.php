<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'deadline_mode')) {
                $table->boolean('deadline_mode')->default(false)->after('scheduled_at');
            }

            if (!Schema::hasColumn('announcements', 'deadline_at')) {
                $table->timestamp('deadline_at')->nullable()->after('deadline_mode');
            }

            if (!Schema::hasColumn('announcements', 'reminder_enabled')) {
                $table->boolean('reminder_enabled')->default(false)->after('deadline_at');
            }

            if (!Schema::hasColumn('announcements', 'reminder_offset_value')) {
                $table->unsignedSmallInteger('reminder_offset_value')->nullable()->after('reminder_enabled');
            }

            if (!Schema::hasColumn('announcements', 'reminder_offset_unit')) {
                $table->string('reminder_offset_unit', 10)->nullable()->after('reminder_offset_value');
            }

            if (!Schema::hasColumn('announcements', 'reminder_at')) {
                $table->timestamp('reminder_at')->nullable()->after('reminder_offset_unit');
                $table->index('reminder_at');
            }

            if (!Schema::hasColumn('announcements', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('reminder_at');
            }

            if (!Schema::hasColumn('announcements', 'reminder_send_status')) {
                $table->string('reminder_send_status', 20)->nullable()->after('reminder_sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (Schema::hasColumn('announcements', 'reminder_at')) {
                $table->dropIndex(['reminder_at']);
            }

            $columns = [
                'deadline_mode',
                'deadline_at',
                'reminder_enabled',
                'reminder_offset_value',
                'reminder_offset_unit',
                'reminder_at',
                'reminder_sent_at',
                'reminder_send_status',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('announcements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

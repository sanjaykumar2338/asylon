<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('report_chat_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('report_chat_messages', 'sent_at')) {
                $columnAfter = Schema::hasColumn('report_chat_messages', 'body') ? 'body' : 'message';
                $table->timestamp('sent_at')->nullable()->useCurrent()->after($columnAfter);
            }
        });

        if (Schema::hasColumn('report_chat_messages', 'from')) {
            DB::statement('ALTER TABLE report_chat_messages CHANGE `from` `side` VARCHAR(32) NOT NULL');
        }

        if (Schema::hasColumn('report_chat_messages', 'body')) {
            DB::statement('ALTER TABLE report_chat_messages CHANGE `body` `message` TEXT NOT NULL');
        }

        if (Schema::hasColumn('report_chat_messages', 'sent_at')) {
            DB::statement('UPDATE report_chat_messages SET sent_at = created_at WHERE sent_at IS NULL');
            DB::statement('ALTER TABLE report_chat_messages MODIFY `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('report_chat_messages', 'message')) {
            DB::statement('ALTER TABLE report_chat_messages CHANGE `message` `body` TEXT NOT NULL');
        }

        if (Schema::hasColumn('report_chat_messages', 'side')) {
            DB::statement('ALTER TABLE report_chat_messages CHANGE `side` `from` VARCHAR(255) NOT NULL');
        }

        if (Schema::hasColumn('report_chat_messages', 'sent_at')) {
            DB::statement('ALTER TABLE report_chat_messages DROP COLUMN `sent_at`');
        }
    }
};

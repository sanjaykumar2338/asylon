<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_files', function (Blueprint $table): void {
            if (! Schema::hasColumn('report_files', 'transcription_status')) {
                $table->string('transcription_status')->default('pending')->after('has_sensitive_content');
            }
            if (! Schema::hasColumn('report_files', 'transcript')) {
                $table->text('transcript')->nullable()->after('transcription_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('report_files', function (Blueprint $table): void {
            if (Schema::hasColumn('report_files', 'transcript')) {
                $table->dropColumn('transcript');
            }
            if (Schema::hasColumn('report_files', 'transcription_status')) {
                $table->dropColumn('transcription_status');
            }
        });
    }
};

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
        Schema::table('report_files', function (Blueprint $table): void {
            $table->string('safety_scan_status')->default('pending')->after('size');
            $table->json('safety_scan_reasons')->nullable()->after('safety_scan_status');
            $table->boolean('has_sensitive_content')->default(false)->after('safety_scan_reasons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_files', function (Blueprint $table): void {
            $table->dropColumn(['safety_scan_status', 'safety_scan_reasons', 'has_sensitive_content']);
        });
    }
};

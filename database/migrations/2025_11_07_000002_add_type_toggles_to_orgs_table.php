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
        Schema::table('orgs', function (Blueprint $table): void {
            $table->boolean('enable_commendations')
                ->default(false)
                ->after('on_call_user_id');
            $table->boolean('enable_hr_reports')
                ->default(false)
                ->after('enable_commendations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->dropColumn(['enable_commendations', 'enable_hr_reports']);
        });
    }
};

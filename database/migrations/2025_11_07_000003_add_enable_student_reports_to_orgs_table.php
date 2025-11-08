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
            $table->boolean('enable_student_reports')
                ->default(true)
                ->after('enable_hr_reports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->dropColumn('enable_student_reports');
        });
    }
};

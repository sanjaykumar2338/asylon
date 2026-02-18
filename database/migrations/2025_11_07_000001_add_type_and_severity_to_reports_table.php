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
        Schema::table('reports', function (Blueprint $table): void {
            $table->enum('type', ['safety', 'commendation', 'hr'])
                ->default('safety')
                ->index()
                ->after('org_id');
            $table->enum('severity', ['low', 'moderate', 'high', 'critical'])
                ->default('moderate')
                ->index()
                ->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table): void {
            $table->dropColumn(['type', 'severity']);
        });
    }
};

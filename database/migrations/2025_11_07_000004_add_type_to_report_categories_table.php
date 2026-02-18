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
        Schema::table('report_categories', function (Blueprint $table): void {
            $table->enum('type', ['student', 'employee', 'both'])
                ->default('both')
                ->after('position')
                ->index();
        });

        Schema::table('report_subcategories', function (Blueprint $table): void {
            $table->enum('type', ['student', 'employee', 'both'])
                ->default('both')
                ->after('position')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_subcategories', function (Blueprint $table): void {
            $table->dropColumn('type');
        });

        Schema::table('report_categories', function (Blueprint $table): void {
            $table->dropColumn('type');
        });
    }
};

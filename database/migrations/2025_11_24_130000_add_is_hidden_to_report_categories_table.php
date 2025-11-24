<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_categories', function (Blueprint $table): void {
            if (! Schema::hasColumn('report_categories', 'is_hidden')) {
                $table->boolean('is_hidden')->default(false)->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('report_categories', function (Blueprint $table): void {
            if (Schema::hasColumn('report_categories', 'is_hidden')) {
                $table->dropColumn('is_hidden');
            }
        });
    }
};
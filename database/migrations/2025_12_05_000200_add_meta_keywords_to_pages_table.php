<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pages') && ! Schema::hasColumn('pages', 'meta_keywords')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'meta_keywords')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('meta_keywords');
            });
        }
    }
};

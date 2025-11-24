<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orgs', 'default_locale')) {
            Schema::table('orgs', function (Blueprint $table): void {
                $table->string('default_locale', 8)->default('en')->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orgs', 'default_locale')) {
            Schema::table('orgs', function (Blueprint $table): void {
                $table->dropColumn('default_locale');
            });
        }
    }
};

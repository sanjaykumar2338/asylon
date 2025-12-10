<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->string('short_name')->nullable()->after('name');
            $table->string('org_type')->nullable()->after('short_name');
            $table->string('contact_email')->nullable()->after('default_locale');
            $table->string('contact_phone')->nullable()->after('contact_email');
            $table->string('primary_color')->nullable()->after('contact_phone');
            $table->string('logo_path')->nullable()->after('primary_color');
        });
    }

    public function down(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->dropColumn([
                'short_name',
                'org_type',
                'contact_email',
                'contact_phone',
                'primary_color',
                'logo_path',
            ]);
        });
    }
};

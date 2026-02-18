<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->string('preferred_plan')->nullable()->after('plan_id');
        });

        // Ensure new orgs default to pending billing until checkout completes.
        DB::statement("ALTER TABLE orgs MODIFY billing_status VARCHAR(255) DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->dropColumn('preferred_plan');
        });

        DB::statement("ALTER TABLE orgs MODIFY billing_status VARCHAR(255) DEFAULT 'active'");
    }
};

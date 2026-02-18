<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->foreignId('plan_id')->nullable()->after('id')->constrained('plans')->nullOnDelete();
            $table->string('billing_status')->default('active')->after('plan_id');
            $table->timestamp('trial_ends_at')->nullable()->after('billing_status');
            $table->boolean('is_self_service')->default(false)->after('trial_ends_at');

            $table->unsignedInteger('reports_this_month')->default(0)->after('is_self_service');
            $table->timestamp('reports_month_reset_at')->nullable()->after('reports_this_month');
            $table->unsignedBigInteger('total_reports')->default(0)->after('reports_month_reset_at');
            $table->unsignedInteger('seats_used')->default(0)->after('total_reports');
        });
    }

    public function down(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn([
                'billing_status',
                'trial_ends_at',
                'is_self_service',
                'reports_this_month',
                'reports_month_reset_at',
                'total_reports',
                'seats_used',
            ]);
        });
    }
};

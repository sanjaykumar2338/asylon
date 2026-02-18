<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_subscriptions', function (Blueprint $table): void {
            if (! Schema::hasColumn('billing_subscriptions', 'stripe_price_id')) {
                $table->string('stripe_price_id')->nullable()->after('stripe_subscription_id');
            }

            if (! Schema::hasColumn('billing_subscriptions', 'plan_code')) {
                $table->string('plan_code')->nullable()->after('plan_slug');
            }

            $table->index('stripe_price_id');
        });

        Schema::create('billing_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('type');
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_events');

        Schema::table('billing_subscriptions', function (Blueprint $table): void {
            if (Schema::hasColumn('billing_subscriptions', 'stripe_price_id')) {
                $table->dropColumn('stripe_price_id');
            }

            if (Schema::hasColumn('billing_subscriptions', 'plan_code')) {
                $table->dropColumn('plan_code');
            }
        });
    }
};

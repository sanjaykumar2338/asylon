<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('plan_slug')->nullable();
            $table->string('stripe_payment_id')->nullable()->unique();
            $table->string('stripe_charge_id')->nullable()->index();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('currency', 12)->default('usd');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('paid_at');
            $table->index('plan_slug');
        });

        Schema::create('billing_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('plan_slug')->nullable();
            $table->string('stripe_invoice_id')->nullable()->unique();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('currency', 12)->default('usd');
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('period_start');
            $table->index('plan_slug');
        });

        Schema::create('billing_refunds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('plan_slug')->nullable();
            $table->string('stripe_refund_id')->nullable()->unique();
            $table->string('stripe_charge_id')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('currency', 12)->default('usd');
            $table->string('reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index('refunded_at');
            $table->index('plan_slug');
        });

        Schema::create('billing_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('plan_slug')->nullable();
            $table->string('stripe_subscription_id')->nullable()->unique();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('currency', 12)->default('usd');
            $table->string('interval', 24)->default('month');
            $table->unsignedInteger('interval_count')->default(1);
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('plan_slug');
            $table->index('current_period_end');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_subscriptions');
        Schema::dropIfExists('billing_refunds');
        Schema::dropIfExists('billing_invoices');
        Schema::dropIfExists('billing_payments');
    }
};

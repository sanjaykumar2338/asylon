<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->string('billing_interval'); // monthly, yearly, custom
            $table->boolean('is_early_adopter')->default(false);
            $table->string('stripe_price_id')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['plan_id', 'billing_interval', 'is_early_adopter'], 'plan_interval_early_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};

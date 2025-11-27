<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('min_risk_level')->default('high'); // low, medium, high, critical
            $table->boolean('match_urgent')->default(false);
            $table->string('match_category')->nullable();
            $table->boolean('auto_mark_urgent')->default(false);
            $table->json('notify_roles')->nullable(); // roles to notify
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_rules');
    }
};

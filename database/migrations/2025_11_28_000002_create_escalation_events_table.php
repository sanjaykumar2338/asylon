<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalation_events', function (Blueprint $table): void {
            $table->id();
            $table->string('report_id', 26);
            $table->foreignId('escalation_rule_id')->nullable()->constrained('escalation_rules')->cascadeOnDelete();
            $table->string('rule_name')->nullable();
            $table->json('actions')->nullable();
            $table->timestamps();

            $table->foreign('report_id')
                ->references('id')
                ->on('reports')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalation_events');
    }
};

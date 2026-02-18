<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_risk_analyses', function (Blueprint $table): void {
            $table->id();
            $table->string('report_id', 26);
            $table->foreign('report_id')
                ->references('id')
                ->on('reports')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('risk_score')->default(0);
            $table->string('risk_level')->default('low');
            $table->json('matched_keywords')->nullable();
            $table->json('signals')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_risk_analyses');
    }
};

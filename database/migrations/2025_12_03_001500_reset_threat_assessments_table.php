<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('threat_assessments');

        Schema::create('threat_assessments', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignUlid('report_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->string('level')->default('low');
            $table->text('summary')->nullable();
            $table->json('signals')->nullable();
            $table->string('recommendation')->nullable();
            $table->boolean('subject_of_concern')->default(false);
            $table->timestamps();

            $table->unique('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threat_assessments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('risk_keywords', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained('orgs')->nullOnDelete();
            $table->string('phrase', 200);
            $table->unsignedInteger('weight')->default(20);
            $table->timestamps();

            $table->unique(['org_id', 'phrase']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_keywords');
    }
};

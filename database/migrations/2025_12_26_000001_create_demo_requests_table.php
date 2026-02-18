<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('demo_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('first_name', 120);
            $table->string('last_name', 120);
            $table->string('organization', 255);
            $table->string('organization_type', 80);
            $table->string('role', 120)->nullable();
            $table->string('email', 255);
            $table->string('phone', 80)->nullable();
            $table->string('meeting', 120);
            $table->string('time_window', 80)->nullable();
            $table->text('concerns')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};

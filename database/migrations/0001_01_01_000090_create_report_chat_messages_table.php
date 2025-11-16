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
        Schema::create('report_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('report_id')->constrained('reports')->cascadeOnDelete();
            $table->string('side', 32);
            $table->text('message');
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_chat_messages');
    }
};

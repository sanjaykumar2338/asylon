<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_deletion_requests', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('org_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('requester_type')->nullable();
            $table->string('requester_name')->nullable();
            $table->string('requester_email')->nullable();
            $table->string('requester_phone')->nullable();

            $table->string('scope');
            $table->string('reference_type')->nullable();
            $table->string('reference_value')->nullable();

            $table->enum('status', ['new', 'in_review', 'completed', 'rejected'])->default('new');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('processed_at')->nullable();

            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();

            $table->timestamps();

            $table->index(['org_id', 'status']);
            $table->index(['due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_deletion_requests');
    }
};

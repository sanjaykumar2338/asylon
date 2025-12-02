<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Support\TemplateRenderer;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('org_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('channel', ['sms', 'email']);
            $table->enum('type', ['alert', 'followup', 'urgent_alert']);
            $table->string('subject')->nullable();
            $table->text('body');
            $table->timestamps();

            $table->unique(['org_id', 'channel', 'type']);
        });

        $defaults = config('notification_templates.defaults');

        $rows = [
            [
                'org_id' => null,
                'channel' => 'sms',
                'type' => 'alert',
                'subject' => null,
                'body' => TemplateRenderer::ensureSmsCompliance($defaults['sms']['alert'] ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_id' => null,
                'channel' => 'sms',
                'type' => 'urgent_alert',
                'subject' => null,
                'body' => TemplateRenderer::ensureSmsCompliance($defaults['sms']['urgent_alert'] ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_id' => null,
                'channel' => 'sms',
                'type' => 'followup',
                'subject' => null,
                'body' => TemplateRenderer::ensureSmsCompliance($defaults['sms']['followup'] ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_id' => null,
                'channel' => 'email',
                'type' => 'alert',
                'subject' => $defaults['email']['alert']['subject'] ?? null,
                'body' => $defaults['email']['alert']['body'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_id' => null,
                'channel' => 'email',
                'type' => 'urgent_alert',
                'subject' => $defaults['email']['urgent_alert']['subject'] ?? null,
                'body' => $defaults['email']['urgent_alert']['body'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'org_id' => null,
                'channel' => 'email',
                'type' => 'followup',
                'subject' => $defaults['email']['followup']['subject'] ?? null,
                'body' => $defaults['email']['followup']['body'] ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('notification_templates')->insert($rows);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};

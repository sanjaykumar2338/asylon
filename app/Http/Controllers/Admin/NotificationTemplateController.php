<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationTemplateController extends Controller
{
    /**
     * Display the template management page.
     */
    public function edit(): View
    {
        $orgId = Auth::user()?->org_id;
        $definitions = NotificationTemplate::definitions();
        $templates = [];
        $orgTemplateCount = NotificationTemplate::query()->where('org_id', $orgId)->count();

        foreach ($definitions as $channel => $types) {
            foreach ($types as $type => $meta) {
                $resolved = NotificationTemplate::resolve($orgId, $channel, $type);
                $templates[$channel][$type] = array_merge($resolved, [
                    'label' => $meta['label'] ?? ucfirst($type),
                    'placeholders' => $meta['placeholders'] ?? [],
                ]);
            }
        }

        $complianceLine = NotificationTemplate::smsComplianceLine();
        $defaults = config('notification_templates.defaults');

        return view('admin.notifications.templates', [
            'templates' => $templates,
            'complianceLine' => $complianceLine,
            'defaults' => $defaults,
            'orgId' => $orgId,
            'usingGlobalDefaults' => $orgTemplateCount === 0,
        ]);
    }

    /**
     * Save or restore a template.
     */
    public function update(Request $request): RedirectResponse
    {
        $orgId = Auth::user()?->org_id;

        $data = $request->validate([
            'channel' => ['required', 'in:sms,email'],
            'type' => ['required', 'in:alert,followup,urgent_alert'],
            'subject' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:4000'],
            'restore' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('restore')) {
            NotificationTemplate::query()
                ->where('channel', $data['channel'])
                ->where('type', $data['type'])
                ->where('org_id', $orgId)
                ->delete();

            return back()->with('ok', 'Template restored to global default.');
        }

        NotificationTemplate::saveTemplate(
            $orgId,
            $data['channel'],
            $data['type'],
            $data['subject'] ?? null,
            $data['body'] ?? null,
        );

        return back()->with('ok', 'Template updated.');
    }
}

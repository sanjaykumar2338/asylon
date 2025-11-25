<x-guest-layout container-class="w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="flex flex-col gap-4 border-b border-gray-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
            <h1 class="text-3xl font-semibold text-gray-900">Asylon Support</h1>
            <p class="mt-2 text-sm text-gray-600">
                Support is for administrators and staff using the system. It is not for emergency reporting.
            </p>
        </div>
        <div class="rounded-md border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-900 sm:max-w-xs space-y-3">
            <div>
                <p class="font-semibold">General questions</p>
                <a href="mailto:{{ $infoEmail ?? 'info@asylon.cc' }}" class="font-semibold text-indigo-700 underline">{{ $infoEmail ?? 'info@asylon.cc' }}</a>
                <p class="mt-1 text-xs text-indigo-800">
                    Outreach, partnerships, and non-technical inquiries.
                </p>
            </div>
            <div>
                <p class="font-semibold">Technical support</p>
                <a href="mailto:{{ $supportEmail }}" class="font-semibold text-indigo-700 underline">{{ $supportEmail }}</a>
                <p class="mt-1 text-xs text-indigo-800">
                    Include your organization name, role, and feature details when requesting assistance.
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 space-y-8 text-gray-800">
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">How to get help</h2>
            <p class="text-sm text-gray-700">
                Email <a href="mailto:{{ $infoEmail ?? 'info@asylon.cc' }}" class="text-indigo-700 underline">{{ $infoEmail ?? 'info@asylon.cc' }}</a> for general questions, or <a href="mailto:{{ $supportEmail }}" class="text-indigo-700 underline">{{ $supportEmail }}</a> with a short summary of what you need. We route requests to the right Asylon specialist for your organization.
            </p>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Share your organization name, role, and the case or page you are working in.</li>
                <li>For urgent safety situations, follow your emergency procedures and submit reports through the portal instead of email.</li>
                <li>Attach screenshots or error messages so we can resolve the issue quickly.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Technical questions about the dashboard</h2>
            <p class="text-sm text-gray-700">
                If you are having trouble signing in, updating reviewers, or managing alerts, contact <a href="mailto:{{ $supportEmail }}" class="text-indigo-700 underline">{{ $supportEmail }}</a>. Common requests include:
            </p>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Login access, multi-factor prompts, or account permissions.</li>
                <li>Organization configuration, reviewer roles, and on-call assignments.</li>
                <li>Notifications, SMS/email delivery, and follow-up message issues.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Reporting an issue or bug</h2>
            <p class="text-sm text-gray-700">
                If something looks wrong or a feature is not working, send details to <a href="mailto:{{ $supportEmail }}" class="text-indigo-700 underline">{{ $supportEmail }}</a>.
            </p>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Steps you took before the issue appeared and what you expected to happen.</li>
                <li>Any report IDs, timestamps, or browser/device details that help us reproduce the problem.</li>
                <li>Screenshots or short clips to show the behavior.</li>
            </ul>
        </section>

        <div class="rounded-lg border border-gray-200 bg-gray-50 px-6 py-5 text-sm text-gray-800">
            <p class="font-semibold text-gray-900">Need something else?</p>
            <p class="mt-2">
                We are here to support administrators and staff who keep your community safe. Email
                <a href="mailto:{{ $infoEmail ?? 'info@asylon.cc' }}" class="font-semibold text-indigo-700 underline">{{ $infoEmail ?? 'info@asylon.cc' }}</a>
                or
                <a href="mailto:{{ $supportEmail }}" class="font-semibold text-indigo-700 underline">{{ $supportEmail }}</a>
                and we will follow up with next steps.
            </p>
        </div>
    </div>
</x-guest-layout>

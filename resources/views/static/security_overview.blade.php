<x-guest-layout container-class="w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="border-b border-gray-200 pb-6">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="text-3xl font-semibold text-gray-900">Security Overview</h1>
        <p class="mt-2 text-sm text-gray-700 max-w-3xl">
            A quick summary of how we secure reports, attachments, and follow-up conversations.
        </p>
    </div>

    <div class="mt-8 space-y-8 text-gray-800">
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Data in transit &amp; at rest</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>HTTPS everywhere; signed links for attachment previews and downloads.</li>
                <li>Stored files are protected in restricted storage with access limited to authorized reviewers.</li>
                <li>Audit logs track administrative actions for accountability.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Anonymity safeguards</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Anonymous submissions by default; contact info optional.</li>
                <li>Voice anonymization available for uploaded/recorded audio.</li>
                <li>IP and device details are suppressed when ultra-private mode is enabled.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Data retention &amp; access</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Retention follows your organization’s policies; data can be removed upon authorized requests.</li>
                <li>Role-based access limits who can view or act on reports.</li>
                <li>No third-party analytics or tracking on reporting portals.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Operational practices</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Signed URLs for attachments expire and cannot be guessed.</li>
                <li>Alerts are routed to authorized administrators and reviewers only.</li>
                <li>Incident response playbooks ensure timely review of urgent cases.</li>
            </ul>
        </section>

        <div class="rounded-lg border border-indigo-100 bg-indigo-50 px-6 py-5 text-sm text-indigo-900">
            <p class="font-semibold text-indigo-900">Questions or requests?</p>
            <p class="mt-2">
                Reach us at <a href="mailto:{{ config('asylon.info_email', 'info@asylon.cc') }}" class="font-semibold underline">{{ config('asylon.info_email', 'info@asylon.cc') }}</a>
                or <a href="mailto:{{ config('asylon.support_email', 'support@asylon.cc') }}" class="font-semibold underline">{{ config('asylon.support_email', 'support@asylon.cc') }}</a>.
            </p>
        </div>
    </div>
</x-guest-layout>

<x-guest-layout container-class="w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="border-b border-gray-200 pb-6">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="text-3xl font-semibold text-gray-900">Privacy &amp; Anonymity</h1>
        <p class="mt-2 text-sm text-gray-700 max-w-3xl">
            We protect reporters by default. Your identity stays private unless <strong>you</strong> choose to share it. Below is a plain-language summary of what we collect, how anonymity works, and how long we retain data.
        </p>
    </div>

    <div class="mt-8 space-y-8 text-gray-800">
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">What we collect — and what we don’t</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li><strong>Report details you provide</strong> (description, attachments, optional contact info).</li>
                <li><strong>Optional follow-up contact</strong> if you choose to share your email/phone.</li>
                <li><strong>We do not require names</strong>, sign-ins, or personal identifiers to submit.</li>
                <li><strong>No tracking cookies or analytics</strong> on the reporting and follow-up pages.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">How anonymity works</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>You can submit entirely anonymous reports—leave contact fields blank to stay anonymous.</li>
                <li>If you share contact info, it’s only used for follow-up about your report.</li>
                <li>Voice recordings can be automatically anonymized to disguise your voice.</li>
                <li>Internal audit logs are limited to authorized reviewers and administrators.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">How voice and attachments are stored</h2>
            <p class="text-sm text-gray-700">
                Files are stored securely with access limited to authorized reviewers. Sensitive audio is queued for anonymization, and all downloads are signed to prevent public access.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Data retention &amp; deletion</h2>
            <p class="text-sm text-gray-700">
                Reports and files are retained only as long as required by your organization’s policies. Deletion requests can be made through your administrator. When the “ultra-private” mode is enabled, IP and device details are suppressed on intake.
            </p>
        </section>

        <div class="rounded-lg border border-indigo-100 bg-indigo-50 px-6 py-5 text-sm text-indigo-900">
            <p class="font-semibold text-indigo-900">Questions?</p>
            <p class="mt-2">
                Contact <a href="mailto:{{ config('asylon.info_email', 'info@asylon.cc') }}" class="font-semibold underline">{{ config('asylon.info_email', 'info@asylon.cc') }}</a>
                or <a href="mailto:{{ config('asylon.support_email', 'support@asylon.cc') }}" class="font-semibold underline">{{ config('asylon.support_email', 'support@asylon.cc') }}</a>.
            </p>
        </div>
    </div>
</x-guest-layout>

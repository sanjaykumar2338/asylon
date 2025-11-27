<x-guest-layout container-class="w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="border-b border-gray-200 pb-6 space-y-2">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="text-3xl font-semibold text-gray-900">Asylon Brand &amp; SMS Alerts Information</h1>
        <p class="text-sm text-gray-700 max-w-3xl">
            This page is provided for carriers to verify Asylon’s SMS program, opt-in workflow, and message flow requirements.
        </p>
        <p class="text-sm text-gray-700 max-w-3xl">
            For carrier verification, please review this page: <strong>{{ url('/brand-info') }}</strong>
        </p>
    </div>

    <div class="mt-8 space-y-8 text-gray-800">
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Brand details</h2>
            <p><strong>Brand name:</strong> Asylon</p>
            <p><strong>Purpose:</strong> Asylon is a safety reporting platform used by schools and organizations to collect and respond to safety, incident, and security-related reports.</p>
            <img src="{{ asset('assets/images/sample.jpeg') }}" alt="Sample SMS alert" class="rounded border mt-2 max-w-full h-auto">
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Who receives SMS messages?</h2>
            <p>
                SMS messages are sent <strong>only</strong> to authorized internal staff such as administrators, counselors, HR, and security personnel. The general public and students are not added to SMS alert lists.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">How phone numbers are added</h2>
            <p>
                Staff phone numbers are added by the school or organization’s administrator from internal HR records during onboarding. Phone numbers are not collected via public web forms.
            </p>
            <p>
                Phone numbers are added from internal HR records by the organization’s administrator. Staff do not enter their own numbers into public forms.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Opt-in process</h2>
            <p>
                During onboarding, staff are informed that they will receive internal safety, security, and incident alerts from Asylon via SMS as part of their role. By acknowledging onboarding materials and using the platform, staff consent to receiving these internal alerts.
            </p>
            <p>
                During onboarding, staff acknowledge that they will receive safety and incident alerts via SMS as part of their internal role. This acknowledgment serves as the opt-in confirmation required by carriers.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Message types</h2>
            <p>
                Messages are limited to internal safety, incident, and security-related alerts. No promotional or marketing SMS messages are sent.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Opt-out instructions</h2>
            <p>
                Staff can opt out at any time by replying <strong>STOP</strong> to an SMS message. After opting out, they will no longer receive SMS alerts unless they are re-enrolled by their organization and consent again.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Support</h2>
            <p>
                For questions about SMS alerts or your data, please contact the Asylon support team at
                <a href="mailto:{{ config('asylon.support_email', 'support@asylon.cc') }}" class="text-indigo-700 underline">{{ config('asylon.support_email', 'support@asylon.cc') }}</a>.
            </p>
        </section>
    </div>
</x-guest-layout>

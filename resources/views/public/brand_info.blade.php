<x-guest-layout container-class="w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="border-b border-gray-200 pb-6 space-y-2">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="text-3xl font-semibold text-gray-900">Asylon Brand &amp; SMS Alerts Information</h1>
        <p class="text-sm text-gray-700 max-w-3xl">
            This page is provided for carriers to verify Asylon's SMS program, opt-in workflow, and message flow requirements.
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
                Staff phone numbers are added by the school or organization's administrator from internal HR records during onboarding. Phone numbers are not collected via public web forms or marketing lists.
            </p>
            <p>
                Phone numbers come directly from the organization's internal HR systems; staff do not enter their own numbers into public web forms or self-enrollment pages.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Opt-in process</h2>
            <p>
                During onboarding, staff are informed that they will receive internal safety, security, and incident alerts from Asylon via SMS as part of their role. By acknowledging onboarding materials and using the platform, staff consent to receiving these internal alerts.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Opt-in confirmation (required for carriers)</h2>
            <p>
                During onboarding, staff are informed in writing (digital onboarding packet or HR enrollment documents) that their phone number will be added to the internal safety alert system. By acknowledging the onboarding documents and completing the HR enrollment, staff provide consent ("opt-in") to receive safety, security, and incident alerts from Asylon.
            </p>
            <p>
                Phone numbers are <strong>not</strong> collected online or via public forms. All numbers come from internal HR systems maintained by the school or organization.
            </p>
            <p>
                During onboarding, staff complete the organization's official HR enrollment packet, which includes written consent to receive internal safety and incident alerts via SMS from Asylon. This written acknowledgment serves as the required opt-in confirmation. Phone numbers are NOT collected online or via public forms. All numbers come from internal HR systems maintained by the organization.
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

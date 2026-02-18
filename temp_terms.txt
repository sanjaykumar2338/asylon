@php
    $appName = config('app.name', 'Asylon');
@endphp

<x-guest-layout container-class="w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="border-b border-gray-200 pb-6">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="mt-2 text-3xl font-semibold text-gray-900">Terms of Use</h1>
        <p class="mt-3 text-sm text-gray-600">
            These terms govern access to the {{ $appName }} safety and reporting platform for authorized schools and organizations.
        </p>
    </div>

    <div class="mt-8 space-y-8 text-gray-800">
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Acceptance of terms</h2>
            <p class="text-sm text-gray-700">
                By accessing or using {{ $appName }}, you confirm that you have authority to act on behalf of your organization and agree to comply with these terms and all applicable policies.
                If you do not agree, do not use the platform.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Use of the platform</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>{{ $appName }} is provided to support school safety, incident intake, and follow-up communication.</li>
                <li>You are responsible for maintaining the confidentiality of your credentials and limiting access to authorized staff.</li>
                <li>Submit accurate information and respect the privacy of reporters and impacted individuals.</li>
                <li>Follow your organization’s internal policies and any additional agreements in place with Asylon.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Privacy &amp; data protection</h2>
            <p class="text-sm text-gray-700">
                Asylon processes information to deliver and improve the service, and uses safeguards designed to protect the confidentiality and integrity of report data.
                Avoid including unnecessary personal information when submitting reports or attachments.
            </p>
            <p class="text-sm text-gray-700">
                @if (! empty($privacyPolicyUrl))
                    Review our <a href="{{ $privacyPolicyUrl }}" class="text-indigo-700 underline">Privacy Policy</a> for details on data handling.
                @else
                    For privacy details, refer to your organization’s agreement with Asylon or request a copy of the Privacy Policy at <a href="mailto:{{ $supportEmail }}" class="text-indigo-700 underline">{{ $supportEmail }}</a>.
                @endif
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Prohibited use</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Sharing access credentials or attempting to access accounts or data you are not authorized to view.</li>
                <li>Altering, disabling, or interfering with platform security or notification settings.</li>
                <li>Uploading malicious files or content that infringes on privacy, confidentiality, or intellectual property rights.</li>
                <li>Using the platform as a substitute for emergency response—call local emergency services for time-sensitive threats.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Limitation of liability</h2>
            <p class="text-sm text-gray-700">
                {{ $appName }} is provided to assist authorized staff in managing reports and communications. Your organization remains responsible for investigating, responding to, and resolving incidents.
                To the fullest extent permitted by law, Asylon is not liable for indirect, incidental, or consequential damages arising from use of the platform.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Contact information</h2>
            <p class="text-sm text-gray-700">
                Questions about these terms, privacy, or data handling can be sent to
                <a href="mailto:{{ $supportEmail }}" class="text-indigo-700 underline">{{ $supportEmail }}</a>.
            </p>
        </section>
    </div>
</x-guest-layout>

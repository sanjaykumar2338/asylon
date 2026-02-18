<x-guest-layout container-class="w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <div class="border-b border-gray-200 pb-6">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="mt-2 text-3xl font-semibold text-gray-900">Privacy Policy</h1>
        <p class="mt-3 text-sm text-gray-600">
            This policy describes how Asylon collects, protects, and uses information provided through our reporting platform.
        </p>
    </div>

    <div class="mt-8 space-y-8 text-gray-800">
        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Information we collect</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Details submitted in report forms, attachments, and optional contact information.</li>
                <li>Metadata such as timestamps, IP addresses, browser details, and organization selections.</li>
                <li>Messages exchanged through the follow-up portal.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">How we use information</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Deliver reports to the appropriate organization and authorized reviewers.</li>
                <li>Provide follow-up communication between reporters and safety teams.</li>
                <li>Notify on-call responders when urgent reports are submitted.</li>
                <li>Improve the reliability and security of the platform.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">How we protect information</h2>
            <p class="text-sm text-gray-700">
                We use encryption at rest and in transit, access controls, and logging to keep report data secure. Only authorized personnel and contracted organizations have access. Voice recordings can be anonymized before delivery.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Retention and deletion</h2>
            <p class="text-sm text-gray-700">
                Report data is retained according to each organization's policy and legal obligations. We honor verified deletion requests submitted by organization administrators or legal contacts.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Your choices</h2>
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Reports can be submitted anonymously; sharing contact details is optional.</li>
                <li>You may use voice recordings or typed descriptions depending on your preference.</li>
                <li>Administrators can request exports, updates, or deletion through our support team.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-900">Contact</h2>
            <p class="text-sm text-gray-700">
                Privacy questions can be emailed to <a href="mailto:{{ $supportEmail }}" class="text-indigo-700 underline">{{ $supportEmail }}</a> or <a href="mailto:{{ $infoEmail }}" class="text-indigo-700 underline">{{ $infoEmail }}</a>.
            </p>
        </section>
    </div>
</x-guest-layout>

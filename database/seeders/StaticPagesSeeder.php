<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class StaticPagesSeeder extends Seeder
{
    public function run(): void
    {
        $supportEmail = config('asylon.support_email', 'support@asylon.cc');
        $infoEmail = config('asylon.info_email', 'info@asylon.cc');

        Page::updateOrCreate(
            ['slug' => 'support'],
            [
                'title' => 'Asylon Support',
                'excerpt' => 'Support for administrators and staff using the system. Not for emergency reporting.',
                'content' => $this->supportContent($supportEmail, $infoEmail),
                'meta_title' => 'Support | Asylon',
                'meta_description' => 'How to get technical support and contact the Asylon team.',
                'template' => 'static_support',
                'published' => true,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Privacy Policy',
                'excerpt' => 'How Asylon collects, protects, and uses information provided through our reporting platform.',
                'content' => $this->privacyContent(),
                'meta_title' => 'Privacy Policy | Asylon',
                'meta_description' => 'Learn how Asylon protects data and respects privacy for reporters and organizations.',
                'template' => 'static_privacy',
                'published' => true,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'terms'],
            [
                'title' => 'Terms of Use',
                'excerpt' => 'Terms governing access to the Asylon safety and reporting platform for authorized schools and organizations.',
                'content' => $this->termsContent(),
                'meta_title' => 'Terms of Use | Asylon',
                'meta_description' => 'Terms for using the Asylon safety, reporting, and follow-up platform.',
                'template' => 'static_terms',
                'published' => true,
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'submit-report'],
            [
                'title' => 'Submit a Report',
                'excerpt' => 'You stay anonymous unless you choose to share your information.',
                'content' => $this->submitReportContent(),
                'meta_title' => 'Submit a Report | Asylon',
                'meta_description' => 'Submit a concern anonymously or with contact details. Your identity is protected unless you choose to share it.',
                'template' => 'submit_report',
                'published' => true,
            ]
        );
    }

    protected function supportContent(string $supportEmail, string $infoEmail): string
    {
        $infoMailto = "mailto:{$infoEmail}";
        $supportMailto = "mailto:{$supportEmail}";

        return <<<HTML
<section class="border-b border-gray-200 pb-6">
    <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
    <h1 class="text-3xl font-semibold text-gray-900">Asylon Support</h1>
    <p class="mt-2 text-sm text-gray-600">
        Support is for administrators and staff using the system. It is not for emergency reporting.
    </p>
</section>

<section class="mt-8 space-y-8 text-gray-800">
    <div class="rounded-md border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-900 space-y-3">
        <div>
            <p class="font-semibold">General questions</p>
            <a href="{$infoMailto}" class="font-semibold text-indigo-700 underline">{$infoEmail}</a>
            <p class="mt-1 text-xs text-indigo-800">
                Outreach, partnerships, and non-technical inquiries.
            </p>
        </div>
        <div>
            <p class="font-semibold">Technical support</p>
            <a href="{$supportMailto}" class="font-semibold text-indigo-700 underline">{$supportEmail}</a>
            <p class="mt-1 text-xs text-indigo-800">
                Include your organization name, role, and feature details when requesting assistance.
            </p>
        </div>
    </div>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">How to get help</h2>
        <p class="text-sm text-gray-700">
            Email <a href="{$infoMailto}" class="text-indigo-700 underline">{$infoEmail}</a> for general questions, or <a href="{$supportMailto}" class="text-indigo-700 underline">{$supportEmail}</a> with a short summary of what you need. We route requests to the right Asylon specialist for your organization.
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
            If you are having trouble signing in, updating reviewers, or managing alerts, contact <a href="{$supportMailto}" class="text-indigo-700 underline">{$supportEmail}</a>. Common requests include:
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
            If something looks wrong or a feature is not working, send details to <a href="{$supportMailto}" class="text-indigo-700 underline">{$supportEmail}</a>.
        </p>
        <ul class="list-disc space-y-2 pl-5 text-sm">
            <li>Steps you took before the issue appeared and what you expected to happen.</li>
            <li>Any report IDs, timestamps, or browser/device details that help us reproduce the problem.</li>
        </ul>
    </section>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">Security &amp; privacy questions</h2>
        <p class="text-sm text-gray-700">
            For security, privacy, or data requests, use <a href="{$supportMailto}" class="text-indigo-700 underline">{$supportEmail}</a> and include your organization and role so we can verify access before sharing details.
        </p>
    </section>
</section>
HTML;
    }

    protected function privacyContent(): string
    {
        return <<<HTML
<section class="border-b border-gray-200 pb-6">
    <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
    <h1 class="mt-2 text-3xl font-semibold text-gray-900">Privacy Policy</h1>
    <p class="mt-3 text-sm text-gray-600">
        This policy describes how Asylon collects, protects, and uses information provided through our reporting platform.
    </p>
</section>

<section class="mt-8 space-y-8 text-gray-800">
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
        <h2 class="text-xl font-semibold text-gray-900">Sharing information</h2>
        <p class="text-sm text-gray-700">
            Information is shared with the organization selected in the report and with authorized responders for that organization. We do not sell personal information. Service providers only receive the minimum necessary data to deliver the service.
        </p>
    </section>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">Your choices</h2>
        <p class="text-sm text-gray-700">
            Reporters may submit anonymously or include contact details. Organizations control who can access their reports. Contact support to request access or deletion consistent with legal obligations.
        </p>
    </section>
</section>
HTML;
    }

    protected function termsContent(): string
    {
        $appName = config('app.name', 'Asylon');

        return <<<HTML
<section class="border-b border-gray-200 pb-6">
    <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
    <h1 class="mt-2 text-3xl font-semibold text-gray-900">Terms of Use</h1>
    <p class="mt-3 text-sm text-gray-600">
        These terms govern access to the {$appName} safety and reporting platform for authorized schools and organizations.
    </p>
</section>

<section class="mt-8 space-y-8 text-gray-800">
    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">Acceptance of terms</h2>
        <p class="text-sm text-gray-700">
            By accessing or using {$appName}, you confirm that you have authority to act on behalf of your organization and agree to comply with these terms and all applicable policies.
            If you do not agree, do not use the platform.
        </p>
    </section>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">Use of the platform</h2>
        <ul class="list-disc space-y-2 pl-5 text-sm">
            <li>{$appName} is provided to support school safety, incident intake, and follow-up communication.</li>
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
            A separate privacy policy describes how we handle personal data and reporter information.
        </p>
    </section>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">Acceptable use</h2>
        <ul class="list-disc space-y-2 pl-5 text-sm">
            <li>Do not attempt to access reports for organizations you are not authorized to view.</li>
            <li>Do not submit false information or misuse the platform to harass others.</li>
            <li>Do not interfere with or disrupt the service, security controls, or data.</li>
        </ul>
    </section>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">Changes to the service</h2>
        <p class="text-sm text-gray-700">
            We may update or improve features from time to time. We will aim to provide reasonable notice of material changes where possible.
        </p>
    </section>

    <section class="space-y-3">
        <h2 class="text-xl font-semibold text-gray-900">Contact</h2>
        <p class="text-sm text-gray-700">
            For questions about these terms or your organization’s agreement, contact Asylon support.
        </p>
    </section>
</section>
HTML;
    }

    protected function submitReportContent(): string
    {
        return <<<HTML
<p>You stay anonymous unless YOU choose to share your information. Your identity is completely protected.</p>

<p>Your voice matters. Use this form to report a concern, share information, or speak up about something that doesn't feel right. You may remain anonymous if you prefer.</p>

<p>If this is an emergency, please contact 911 immediately.</p>
HTML;
    }
}

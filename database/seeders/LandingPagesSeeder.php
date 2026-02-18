<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingPagesSeeder extends Seeder
{
    public function run(): void
    {
        // Schools
        Page::updateOrCreate(
            ['slug' => 'schools'],
            [
                'title' => 'Anonymous Reporting & Threat Assessment for Schools',
                'excerpt' => 'Asylon gives K–12 schools a safe way for students and staff to report concerns and a structured way for teams to respond.',
                'content' => $this->schoolsContent(),
                'meta_title' => 'Anonymous Reporting System for Schools | Asylon',
                'meta_description' => 'Asylon is a secure anonymous reporting and threat-assessment platform built for K–12 schools and districts.',
                'template' => 'landing_schools',
                'published' => true,
            ]
        );

        // Churches
        Page::updateOrCreate(
            ['slug' => 'churches'],
            [
                'title' => 'Anonymous Reporting for Churches & Ministries',
                'excerpt' => 'Asylon helps churches surface concerns early and protect members with a simple, secure, anonymous reporting process.',
                'content' => $this->churchesContent(),
                'meta_title' => 'Anonymous Reporting System for Churches | Asylon',
                'meta_description' => 'Asylon gives churches a safe, confidential way for members and staff to report concerns before they escalate.',
                'template' => 'landing_churches',
                'published' => true,
            ]
        );

        // Organizations
        Page::updateOrCreate(
            ['slug' => 'organizations'],
            [
                'title' => 'Anonymous Threat Reporting for Organizations',
                'excerpt' => 'Asylon helps organizations and workplaces capture early warning signs and document how concerns are handled.',
                'content' => $this->organizationsContent(),
                'meta_title' => 'Anonymous Threat Reporting Platform for Organizations | Asylon',
                'meta_description' => 'Asylon lets employees and members report threats anonymously while giving leadership a clear, auditable response workflow.',
                'template' => 'landing_organizations',
                'published' => true,
            ]
        );
    }

    protected function schoolsContent(): string
    {
        return <<<HTML
<section data-block="hero">
    <h1>Give Your Students a Safe Way to Speak Up. Give Your Team a Clear Way to Respond.</h1>
    <p>Asylon removes the fear of retaliation by letting students and staff report anonymously from any device, while giving administrators a structured threat-assessment workflow.</p>
</section>

<section data-block="problem">
    <h2>Warning Signs Are Everywhere. They’re Just Not Reaching You.</h2>
    <p>Most concerning behavior is seen by peers first. Without a safe channel, you miss early indicators like bullying, self-harm language, weapons, or harassment.</p>
    <ul>
        <li>Anonymous or named reports in under a minute</li>
        <li>Evidence uploads (photos, audio, screenshots)</li>
        <li>Auto-routing to the right responders</li>
    </ul>
</section>

<section data-block="what-is-asylon">
    <h2>A Simple Anonymous Reporting &amp; Threat-Assessment Platform for Schools</h2>
    <p>Asylon combines intake, triage, documentation, and notifications in one place so your safety team can move fast and stay compliant.</p>
    <ul>
        <li>Anonymous or named reporting via web, QR codes, and shareable links</li>
        <li>Built-in threat indicators and keyword flagging (weapons, self-harm, violence)</li>
        <li>Role-based access for counselors, SROs, admin, and district leads</li>
    </ul>
</section>

<section data-block="how-it-works">
    <h2>From “I’m Worried” to Documented Action – In One Workflow</h2>
    <ol>
        <li><strong>Report.</strong> Student or staff submits concerns anonymously with optional evidence.</li>
        <li><strong>Route.</strong> Asylon auto-routes based on category and urgency to the right responders.</li>
        <li><strong>Respond.</strong> Team members document steps taken, notify contacts, and escalate if needed.</li>
        <li><strong>Learn.</strong> Analytics surface trends (locations, categories, repeat issues) to guide prevention.</li>
    </ol>
</section>

<section data-block="scenarios">
    <h2>Real-World School Scenarios Asylon is Built For</h2>
    <ul>
        <li>Threats of violence or weapons on campus</li>
        <li>Self-harm language and mental-health concerns</li>
        <li>Bullying, cyberbullying, harassment, or discrimination</li>
        <li>Vaping, drugs, and safety violations</li>
    </ul>
</section>

<section data-block="outcomes">
    <h2>Outcomes for Your District</h2>
    <ul>
        <li>More signals captured earlier, with less friction for students</li>
        <li>Documented, repeatable response steps for every case</li>
        <li>District-level visibility and compliance-ready records</li>
    </ul>
</section>

<section data-block="why-asylon">
    <h2>Why Districts Choose Asylon</h2>
    <p>Fast to deploy, intuitive for students, and aligned with threat-assessment best practices. Asylon pairs anonymous intake with clear routing and audit-ready documentation.</p>
</section>

<section data-block="cta">
    <h2>See Asylon With Your Safety Team</h2>
    <p>Request a walkthrough tailored to your school or district. We’ll map your current process to Asylon in one session.</p>
</section>
HTML;
    }

    protected function churchesContent(): string
    {
        return <<<HTML
<section data-block="hero">
    <h1>Give Your Members a Safe, Discreet Way to Speak Up.</h1>
    <p>Asylon helps churches surface concerns early—confidentially—so leaders can respond with care and protect the congregation.</p>
</section>

<section data-block="problem">
    <h2>Many Church Concerns Never Reach Leadership.</h2>
    <p>Members worry about social fallout or not being believed. Without a safe channel, issues like harassment, safety threats, or misconduct stay hidden.</p>
</section>

<section data-block="what-is-asylon">
    <h2>Anonymous Reporting Built for Faith Communities</h2>
    <p>Collect sensitive reports without exposing the reporter. Route them to the right pastoral or safety contacts and document your care response.</p>
    <ul>
        <li>Anonymous or named submissions on web/QR</li>
        <li>Private, role-based access for pastors, elders, and safety leads</li>
        <li>Secure evidence uploads and audit-ready notes</li>
    </ul>
</section>

<section data-block="how-it-works">
    <h2>Simple, Care-Focused Workflow</h2>
    <ol>
        <li><strong>Submit.</strong> Member shares a concern anonymously or with contact info.</li>
        <li><strong>Route.</strong> Direct to the right leaders (pastoral care, security, HR/legal).</li>
        <li><strong>Respond.</strong> Document outreach, support, and next steps.</li>
        <li><strong>Review.</strong> Spot patterns across ministries, campuses, and events.</li>
    </ol>
</section>

<section data-block="scenarios">
    <h2>Scenarios Churches Use Asylon For</h2>
    <ul>
        <li>Harassment, abuse, or misconduct concerns</li>
        <li>Safety threats, stalking, or disruptive individuals</li>
        <li>Wellbeing concerns for staff, volunteers, or members</li>
        <li>Event security tips or facility issues</li>
    </ul>
</section>

<section data-block="outcomes">
    <h2>Outcomes for Your Ministry</h2>
    <ul>
        <li>More people speak up because it feels safe and discreet</li>
        <li>Leaders respond consistently and document care provided</li>
        <li>Visibility across campuses to spot trends early</li>
    </ul>
</section>

<section data-block="why-asylon">
    <h2>Why Ministries Choose Asylon</h2>
    <p>Confidential intake, simple routing, and a compassionate response record—built to fit how churches operate.</p>
</section>

<section data-block="cta">
    <h2>See How It Fits Your Church</h2>
    <p>Schedule a brief walkthrough. We’ll show how members can report safely and how leaders can coordinate responses.</p>
</section>
HTML;
    }

    protected function organizationsContent(): string
    {
        return <<<HTML
<section data-block="hero">
    <h1>Catch Workplace Threats and Concerns Before They Escalate.</h1>
    <p>Asylon gives employees and members a trusted way to report issues—anonymously if needed—while giving leadership a clear, auditable response workflow.</p>
</section>

<section data-block="problem">
    <h2>HR, Security, and Leadership Only See a Fraction of What’s Really Happening.</h2>
    <p>People hesitate to speak up about threats, harassment, or policy violations. Anonymous reporting with structured follow-up closes the gap.</p>
</section>

<section data-block="what-is-asylon">
    <h2>Anonymous Threat &amp; Misconduct Reporting for Organizations</h2>
    <p>Streamline intake, triage, and escalation. Keep evidence, notes, and notifications in one place with role-based visibility.</p>
    <ul>
        <li>Anonymous or named submissions via web/QR</li>
        <li>Category-based routing to HR, security, compliance, or leadership</li>
        <li>Evidence uploads and audit-ready timelines</li>
    </ul>
</section>

<section data-block="how-it-works">
    <h2>One Workflow from Signal to Resolution</h2>
    <ol>
        <li><strong>Submit.</strong> Employee or member shares details and evidence.</li>
        <li><strong>Route.</strong> Auto-assign based on category, severity, and org structure.</li>
        <li><strong>Respond.</strong> Collaborate, document actions, and notify stakeholders.</li>
        <li><strong>Review.</strong> Analytics show patterns by location, department, and type.</li>
    </ol>
</section>

<section data-block="scenarios">
    <h2>Scenarios Organizations Use Asylon For</h2>
    <ul>
        <li>Threats of violence, weapons, or sabotage</li>
        <li>Harassment, discrimination, or retaliation concerns</li>
        <li>Fraud, theft, or policy violations</li>
        <li>Safety hazards or facility issues</li>
    </ul>
</section>

<section data-block="outcomes">
    <h2>Outcomes for Your Organization</h2>
    <ul>
        <li>More signals reported earlier, with less fear</li>
        <li>Consistent, documented responses for every case</li>
        <li>Leadership visibility and audit trails for compliance</li>
    </ul>
</section>

<section data-block="why-asylon">
    <h2>Why Organizations Choose Asylon</h2>
    <p>Fast to deploy, easy to use, and built for sensitive reporting. Asylon pairs anonymous intake with clear routing, escalation, and documentation.</p>
</section>

<section data-block="cta">
    <h2>See Asylon for Your Team</h2>
    <p>Request a tailored walkthrough. We’ll map your current process into Asylon so you can capture early warning signs and respond faster.</p>
</section>
HTML;
    }
}

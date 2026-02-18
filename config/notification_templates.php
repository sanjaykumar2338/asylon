<?php

return [
    'compliance_sms_line' => 'Msg freq may vary. Std rates apply. Reply STOP to opt out, HELP for help.',

    'defaults' => [
        'sms' => [
            'alert' => <<<TEXT
New safety concern reported at {{school_name}} on {{date_time}}.
Category: {{category}}
View report: {{report_link}}
Reply STOP to opt out, HELP for help.
TEXT,
            'urgent_alert' => <<<TEXT
URGENT safety concern at {{school_name}}.
Category: {{category}}
Time: {{date_time}}
View immediately: {{report_link}}
Reply STOP to opt out, HELP for help.
TEXT,
            'followup' => <<<TEXT
Reporter update at {{school_name}}.
Category: {{category}}
Update: {{message}}
View report: {{report_link}}
Reply STOP to opt out, HELP for help.
TEXT,
        ],
        'email' => [
            'alert' => [
                'subject' => 'New Safety Concern Reported – {{school_name}}',
                'body' => <<<TEXT
A new safety concern has been submitted.

School: {{school_name}}
Category: {{category}}
Reported at: {{date_time}}

View full report:
{{report_link}}

This message is sent only to authorized staff per your organization’s internal onboarding policy.
TEXT,
            ],
            'urgent_alert' => [
                'subject' => 'URGENT: New Safety Concern – {{school_name}}',
                'body' => <<<TEXT
URGENT safety concern reported.

School: {{school_name}}
Category: {{category}}
Urgency: {{urgency}}
Reported at: {{date_time}}

View immediately:
{{report_link}}

This message is sent only to authorized staff per your organization’s internal onboarding policy.
TEXT,
            ],
            'followup' => [
                'subject' => 'Reporter Follow-up – {{school_name}}',
                'body' => <<<TEXT
A reporter posted a follow-up message on case #{{report_id}}.

School: {{school_name}}
Category: {{category}}
Reported at: {{date_time}}

Message preview:
{{message}}

View full conversation:
{{report_link}}

This message is sent only to authorized staff per your organization’s internal onboarding policy.
TEXT,
            ],
        ],
    ],
];

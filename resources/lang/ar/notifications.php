<?php

return [
    'labels' => [
        'organization' => 'Organization',
        'category' => 'Category',
        'case_id' => 'Case ID',
        'submitted' => 'Submitted',
        'violation_date' => 'Violation date',
        'not_provided' => 'Not provided',
        'followup_portal' => 'Public follow-up portal:',
        'responder' => 'Responder: :name',
    ],
    'actions' => [
        'open_dashboard' => 'Open in dashboard',
        'open_report' => 'Open report',
    ],
    'urgent_alert' => [
        'badge' => 'Urgent alert',
        'subject' => 'URGENT: New :category report',
        'heading' => 'Urgent case: :category',
        'prompt' => 'Please review and respond as soon as possible.',
    ],
    'reporter_followup' => [
        'badge' => 'Reporter follow-up',
        'subject' => 'Reporter follow-up for :org (:category)',
        'new_message_subject' => 'New message on case #:id',
        'message_intro' => 'Message from reporter:',
        'dashboard_reminder' => 'Please log any responses directly in the dashboard to keep the case history complete.',
    ],
    'first_response' => [
        'subject' => 'First response sent for case #:id',
        'greeting' => 'Hello :name,',
        'body' => 'A reviewer sent the first reply to the reporter.',
        'thanks' => 'Thank you for keeping response times fast.',
    ],
    'assigned_urgent' => [
        'subject' => 'Assigned urgent report: :category',
        'greeting' => 'Hello :name,',
        'body' => 'You have been assigned as the on-call reviewer for a new urgent report.',
        'review_prompt' => 'Please review and respond promptly.',
    ],
    'misc' => [
        'unknown_org' => 'Unknown organization',
        'unknown_name' => 'Unknown',
        'recently' => 'recently',
    ],
];

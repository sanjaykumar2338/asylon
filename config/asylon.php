<?php

$hrCategories = [
    'Harassment and Discrimination' => [
        'Sexual Harassment',
        'Bullying or Verbal Abuse',
        'Discrimination (Gender/Race/Religion/Age)',
        'Unfair Treatment',
    ],
    'Workplace Safety or Conditions' => [
        'Unsafe Working Conditions',
        'Health Hazards',
        'Equipment Safety Issues',
    ],
    'Ethical or Policy Violations' => [
        'Fraud / Misuse of Funds',
        'Conflict of Interest',
        'Violation of Company Policies',
    ],
    'Payroll, Compensation & Benefits' => [
        'Unpaid Wages or Overtime',
        'Incorrect Deductions',
        'Benefit Denial Issues',
    ],
    'Workplace Behavior & Culture' => [
        'Retaliation or Hostility',
        'Favoritism or Nepotism',
        'Toxic Work Environment',
    ],
    'Attendance or Performance Concerns' => [
        'Chronic Absenteeism',
        'Performance Misconduct',
    ],
    'Commendations & Recognition' => [
        'Employee Excellence',
        'Team Achievement',
        'Positive Feedback',
    ],
];

return [
    'languages' => [
        'en' => 'English',
        'es' => 'Spanish',
    ],
    'ffmpeg_path' => env('FFMPEG_PATH', 'ffmpeg'),
    'privacy' => [
        'general' => 'Your report is anonymous unless you choose to share your name or contact information. Asylon is a secure third-party system.',
        'form_header' => 'All reports are anonymous by default. You may provide contact info if you want follow-up.',
        'contact_hint' => 'Optional - leave blank to stay anonymous.',
        'confirm' => 'Thank you for speaking up. You will remain anonymous unless you choose to identify yourself.',
        'email_footer' => 'Sent via Asylon, a third-party reporting system. Your privacy is protected.',
    ],
    'reports' => [
        'description_min_words' => 20,
        'hr_category_map' => $hrCategories,
        'type_categories' => [
            'safety' => [],
            'commendation' => [
                'Positive Reporting',
            ],
            'hr' => array_keys($hrCategories),
        ],
    ],
    'alerts' => [
        'departments' => [
            'security' => 'Campus Security',
            'student_affairs' => 'Student Affairs',
            'counseling' => 'Counseling Services',
            'hr' => 'Human Resources',
            'ethics' => 'Ethics & Compliance',
            'admin' => 'Administration',
        ],
        'student_departments' => ['counseling', 'security', 'student_affairs'],
        'employee_departments' => ['hr', 'ethics', 'admin'],
    ],
    'notifications' => [
        'first_response_org_admin' => env('FIRST_RESPONSE_NOTIFY_ORG_ADMIN', true),
    ],
];

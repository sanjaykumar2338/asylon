<?php

namespace Database\Seeders;

use App\Models\Org;
use App\Models\OrgAlertContact;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportChatMessage;
use App\Models\ReportSubcategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $platformAdmin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make('password123'),
                'role' => 'platform_admin',
                'active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        );

        $org = Org::updateOrCreate(
            ['slug' => 'asylon-high'],
            [
                'name' => 'Asylon High School',
                'status' => 'active',
                'created_by' => $platformAdmin->id,
                'enable_commendations' => true,
                'enable_hr_reports' => true,
            ],
        );
        $this->ensureOrgHasReportCode($org);

        $orgAdmin = User::updateOrCreate(
            ['email' => 'orgadmin@example.com'],
            [
                'name' => 'Org Admin',
                'password' => Hash::make('password123'),
                'role' => 'org_admin',
                'org_id' => $org->id,
                'active' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        );

        $reviewers = [
            ['email' => 'reviewer1@example.com', 'name' => 'Reviewer One'],
            ['email' => 'reviewer2@example.com', 'name' => 'Reviewer Two'],
        ];

        foreach ($reviewers as $reviewer) {
            User::updateOrCreate(
                ['email' => $reviewer['email']],
                [
                    'name' => $reviewer['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'reviewer',
                    'org_id' => $org->id,
                    'active' => true,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ],
            );
        }

        OrgAlertContact::updateOrCreate(
            ['org_id' => $org->id, 'value' => 'security@asylon.edu'],
            [
                'type' => 'email',
                'is_active' => true,
            ],
        );

        OrgAlertContact::updateOrCreate(
            ['org_id' => $org->id, 'value' => '+15551234567'],
            [
                'type' => 'sms',
                'is_active' => true,
            ],
        );

        $categorySeedPath = config_path('report_categories.php');
        $categorySeedData = is_file($categorySeedPath) ? include $categorySeedPath : [];

        $categoryPosition = 1;
        foreach ($categorySeedData as $categoryName => $subcategoryNames) {
            $category = ReportCategory::updateOrCreate(
                ['name' => $categoryName],
                [
                    'description' => null,
                    'position' => $categoryPosition++,
                ],
            );

            $subcategoryPosition = 1;
            foreach ($subcategoryNames as $subcategoryName) {
                ReportSubcategory::updateOrCreate(
                    [
                        'report_category_id' => $category->id,
                        'name' => $subcategoryName,
                    ],
                    [
                        'description' => null,
                        'position' => $subcategoryPosition++,
                    ],
                );
            }
        }

        if (! Report::where('org_id', $org->id)->exists()) {
            $sampleReports = [
                [
                    'category' => 'Safety & Threat-Related',
                    'subcategory' => 'Trespassing / Unauthorized Entry',
                    'description' => 'Motion sensors triggered near the server room after hours. Security footage shows an unidentified individual tailgating through the loading dock.',
                    'type' => 'safety',
                    'severity' => 'high',
                    'urgent' => true,
                    'status' => 'open',
                    'contact_email' => 'anon1@example.com',
                    'violation_date' => now()->subDays(1),
                    'messages' => [
                        ['from' => 'reporter', 'body' => 'I heard alarms and saw someone running out of the side exit.'],
                    ],
                ],
                [
                    'category' => 'Digital & Online Safety',
                    'subcategory' => 'Identity Theft / Impersonation',
                    'description' => 'Multiple staff members received an email claiming to be from IT requesting password resets. Links point to an external domain and spoof the technology director.',
                    'type' => 'safety',
                    'severity' => 'moderate',
                    'urgent' => false,
                    'status' => 'in_review',
                    'contact_email' => 'teacher@example.com',
                    'violation_date' => now()->subDays(2),
                    'messages' => [
                        ['from' => 'reporter', 'body' => 'Email subject was "Urgent Password Reset Required".'],
                        ['from' => 'reviewer', 'body' => 'Thanks for flagging - please forward one of the emails to security.'],
                    ],
                    'first_response_minutes' => 120,
                ],
                [
                    'category' => 'Academic Integrity & Conduct',
                    'subcategory' => 'Theft / Stealing',
                    'description' => 'A Chromebook cart is missing from the science wing. Last seen during evening cleanup on Tuesday.',
                    'type' => 'safety',
                    'severity' => 'high',
                    'urgent' => true,
                    'status' => 'in_review',
                    'contact_phone' => '+15551230001',
                    'violation_date' => now()->subDays(3),
                    'messages' => [
                        ['from' => 'reporter', 'body' => 'We noticed the missing cart during first period.'],
                        ['from' => 'reviewer', 'body' => 'We are reviewing badge access logs for the wing.'],
                    ],
                    'first_response_minutes' => 90,
                ],
                [
                    'category' => 'Bullying & Harassment',
                    'subcategory' => 'Cyberbullying / Online Harassment',
                    'description' => 'Student reports ongoing harassment on social media involving threatening language and sharing of private photos.',
                    'type' => 'hr',
                    'severity' => 'critical',
                    'urgent' => true,
                    'status' => 'open',
                    'contact_name' => 'Concerned Student',
                    'violation_date' => now()->subDays(4),
                    'messages' => [],
                ],
                [
                    'category' => 'Digital & Online Safety',
                    'subcategory' => 'Inappropriate Internet Use',
                    'description' => 'Students discovered a proxy site on library computers that bypassed web filters and disrupted service for 30 minutes on Monday.',
                    'type' => 'commendation',
                    'severity' => 'low',
                    'urgent' => false,
                    'status' => 'closed',
                    'contact_email' => 'librarian@example.com',
                    'violation_date' => now()->subDays(5),
                    'messages' => [
                        ['from' => 'reporter', 'body' => 'Outage occurred around 10:15 AM.'],
                        ['from' => 'reviewer', 'body' => 'Resolved - switch stack rebooted and firmware updated.'],
                    ],
                    'first_response_minutes' => 45,
                ],
                [
                    'category' => 'Safety & Threat-Related',
                    'subcategory' => 'Vandalism / Property Damage',
                    'description' => 'Graffiti found on the north stairwell referencing a potential fight after school.',
                    'type' => 'safety',
                    'severity' => 'high',
                    'urgent' => true,
                    'status' => 'open',
                    'contact_phone' => '+15551230099',
                    'violation_date' => now()->subDays(1),
                    'messages' => [
                        ['from' => 'reporter', 'body' => 'Message said "See you at 4 by the parking lot".'],
                    ],
                ],
            ];

            foreach ($sampleReports as $data) {
                $report = Report::create([
                    'org_id' => $org->id,
                    'category' => $data['category'],
                    'subcategory' => $data['subcategory'] ?? null,
                    'type' => $data['type'] ?? 'safety',
                    'severity' => $data['severity'] ?? 'moderate',
                    'description' => $data['description'],
                    'violation_date' => $data['violation_date'] ?? null,
                    'contact_name' => $data['contact_name'] ?? null,
                    'contact_email' => $data['contact_email'] ?? null,
                    'contact_phone' => $data['contact_phone'] ?? null,
                    'urgent' => $data['urgent'],
                    'status' => $data['status'],
                    'chat_token' => (string) Str::uuid(),
                    'first_response_at' => isset($data['first_response_minutes'])
                        ? now()->subMinutes($data['first_response_minutes'])
                        : null,
                ]);

                foreach ($data['messages'] as $message) {
                    ReportChatMessage::create([
                        'report_id' => $report->getKey(),
                        'from' => $message['from'],
                        'body' => $message['body'],
                    ]);
                }
            }
        }
    }

    /**
     * Ensure the seeded organization has a public report link.
     */
    protected function ensureOrgHasReportCode(Org $org): void
    {
        if (blank($org->org_code)) {
            $org->regenerateReportCode();
        }
    }
}


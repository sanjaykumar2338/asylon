<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Org;
use App\Models\OrgAlertContact;
use App\Models\Page;
use App\Models\SeoPage;
use App\Models\Plan;
use Database\Seeders\BlogSeeder;
use Database\Seeders\LandingPagesSeeder;
use Database\Seeders\StaticPagesSeeder;
use Database\Seeders\PlanSeeder;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportChatMessage;
use App\Models\ReportSubcategory;
use App\Models\RiskKeyword;
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
        $this->call([
            PlanSeeder::class,
        ]);

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
                'enable_student_reports' => true,
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
                'department' => 'security',
                'is_active' => true,
            ],
        );

        OrgAlertContact::updateOrCreate(
            ['org_id' => $org->id, 'value' => '+15551234567'],
            [
                'type' => 'sms',
                'department' => 'security',
                'is_active' => true,
            ],
        );

        OrgAlertContact::updateOrCreate(
            ['org_id' => $org->id, 'value' => 'counseling@asylon.edu'],
            [
                'type' => 'email',
                'department' => 'counseling',
                'is_active' => true,
            ],
        );

        OrgAlertContact::updateOrCreate(
            ['org_id' => $org->id, 'value' => 'hr@asylon.edu'],
            [
                'type' => 'email',
                'department' => 'hr',
                'is_active' => true,
            ],
        );

        OrgAlertContact::updateOrCreate(
            ['org_id' => $org->id, 'value' => 'ethics@asylon.edu'],
            [
                'type' => 'email',
                'department' => 'ethics',
                'is_active' => true,
            ],
        );

        OrgAlertContact::updateOrCreate(
            ['org_id' => $org->id, 'value' => 'admin@asylon.edu'],
            [
                'type' => 'email',
                'department' => 'admin',
                'is_active' => true,
            ],
        );

        $categorySeedPath = config_path('report_categories.php');
        $categorySeedData = is_file($categorySeedPath) ? include $categorySeedPath : [];
        $hrCategories = config('asylon.reports.hr_category_map', []);

        $categoryPosition = 1;
        foreach ($categorySeedData as $categoryName => $subcategoryNames) {
            $category = ReportCategory::updateOrCreate(
                ['name' => $categoryName],
                [
                    'description' => null,
                    'position' => $categoryPosition++,
                    'type' => 'student',
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
                        'type' => 'student',
                    ],
                );
            }
        }

        foreach ($hrCategories as $categoryName => $subcategoryNames) {
            $category = ReportCategory::updateOrCreate(
                ['name' => $categoryName],
                [
                    'description' => null,
                    'position' => $categoryPosition++,
                    'type' => 'employee',
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
                        'type' => 'employee',
                    ],
                );
            }
        }

        $this->seedRiskKeywords($org);
        $this->seedCms($org);

        $this->call([
            LandingPagesSeeder::class,
            StaticPagesSeeder::class,
            BlogSeeder::class,
        ]);

        $this->seedSeoPages();

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
                        ['side' => 'reporter', 'message' => 'I heard alarms and saw someone running out of the side exit.'],
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
                        ['side' => 'reporter', 'message' => 'Email subject was "Urgent Password Reset Required".'],
                        ['side' => 'reviewer', 'message' => 'Thanks for flagging - please forward one of the emails to security.'],
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
                        ['side' => 'reporter', 'message' => 'We noticed the missing cart during first period.'],
                        ['side' => 'reviewer', 'message' => 'We are reviewing badge access logs for the wing.'],
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
                        ['side' => 'reporter', 'message' => 'Outage occurred around 10:15 AM.'],
                        ['side' => 'reviewer', 'message' => 'Resolved - switch stack rebooted and firmware updated.'],
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
                        ['side' => 'reporter', 'message' => 'Message said "See you at 4 by the parking lot".'],
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

                foreach ($data['messages'] as $index => $message) {
                    ReportChatMessage::create([
                        'report_id' => $report->getKey(),
                        'side' => $message['side'] ?? $message['from'] ?? 'reporter',
                        'message' => $message['message'] ?? $message['body'] ?? '',
                        'sent_at' => $message['sent_at'] ?? now()->subMinutes(($index + 1) * 15),
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

    protected function seedRiskKeywords(Org $org): void
    {
        $global = [
            ['phrase' => 'school shooting', 'weight' => 80],
            ['phrase' => 'shoot up the school', 'weight' => 80],
            ['phrase' => 'school shooter', 'weight' => 80],
            ['phrase' => 'bring the heat to school', 'weight' => 70],
            ['phrase' => 'school threat', 'weight' => 70],
            ['phrase' => 'hit the school', 'weight' => 70],
            ['phrase' => 'light up the school', 'weight' => 80],
            ['phrase' => 'schoolyard shooting', 'weight' => 80],
            ['phrase' => 'school lockdown', 'weight' => 60],
            ['phrase' => 'active shooter', 'weight' => 80],
            ['phrase' => 'mass shooting', 'weight' => 80],
            ['phrase' => 'mass violence', 'weight' => 70],
            ['phrase' => 'purge at school', 'weight' => 70],
            ['phrase' => 'revenge at school', 'weight' => 70],
            ['phrase' => 'high score', 'weight' => 50],
            ['phrase' => 'columbine', 'weight' => 70],
            ['phrase' => 'go columbine', 'weight' => 80],
            ['phrase' => 'trench coat mafia', 'weight' => 70],
            ['phrase' => 'virginia tech', 'weight' => 70],
            ['phrase' => 'sandy hook', 'weight' => 70],
            ['phrase' => 'copycat shooting', 'weight' => 70],
            ['phrase' => 'hostile takeover', 'weight' => 60],
            ['phrase' => 'blow up the school', 'weight' => 80],
            ['phrase' => 'bomb threat', 'weight' => 80],
            ['phrase' => 'shoot my classmates', 'weight' => 80],
            ['phrase' => 'kill my teacher', 'weight' => 80],
            ['phrase' => 'bring my piece to school', 'weight' => 70],
            ['phrase' => 'run up in the school', 'weight' => 70],
            ['phrase' => 'snap at school', 'weight' => 50],
            ['phrase' => 'unleash at school', 'weight' => 70],
            ['phrase' => 'bring the smoke to school', 'weight' => 70],
            ['phrase' => 'take them all out', 'weight' => 80],
            ['phrase' => 'today is the day', 'weight' => 50],
            ['phrase' => 'they gone learn today', 'weight' => 50],
            ['phrase' => 'zero day', 'weight' => 70],
            ['phrase' => 'manifesto', 'weight' => 60],
            ['phrase' => 'hit list', 'weight' => 70],
            ['phrase' => 'target list', 'weight' => 70],
            ['phrase' => 'school vengeance', 'weight' => 70],
            ['phrase' => 'school ops', 'weight' => 70],
            ['phrase' => 'switch', 'weight' => 50],
            ['phrase' => 'glock switch', 'weight' => 60],
            ['phrase' => 'auto-switch', 'weight' => 60],
            ['phrase' => 'switchy', 'weight' => 50],
            ['phrase' => 'ghost gun', 'weight' => 70],
            ['phrase' => 'burner', 'weight' => 50],
            ['phrase' => 'strap', 'weight' => 50],
            ['phrase' => 'pole', 'weight' => 50],
            ['phrase' => 'stick', 'weight' => 50],
            ['phrase' => 'long stick', 'weight' => 50],
            ['phrase' => 'draco', 'weight' => 60],
            ['phrase' => 'chopper', 'weight' => 60],
            ['phrase' => 'choppa', 'weight' => 60],
            ['phrase' => 'ar', 'weight' => 50],
            ['phrase' => 'ar-15', 'weight' => 70],
            ['phrase' => 'ak', 'weight' => 50],
            ['phrase' => 'ak-47', 'weight' => 70],
            ['phrase' => 'sks', 'weight' => 50],
            ['phrase' => 'mac-10', 'weight' => 60],
            ['phrase' => 'mac-11', 'weight' => 60],
            ['phrase' => 'tec-9', 'weight' => 60],
            ['phrase' => 'uzi', 'weight' => 60],
            ['phrase' => '9', 'weight' => 50],
            ['phrase' => 'nine', 'weight' => 50],
            ['phrase' => '.40', 'weight' => 50],
            ['phrase' => '.45', 'weight' => 50],
            ['phrase' => 'deuce-deuce', 'weight' => 50],
            ['phrase' => '.25', 'weight' => 50],
            ['phrase' => 'heater', 'weight' => 50],
            ['phrase' => 'iron', 'weight' => 50],
            ['phrase' => 'cannon', 'weight' => 60],
            ['phrase' => 'piece', 'weight' => 50],
            ['phrase' => 'blick', 'weight' => 50],
            ['phrase' => 'blicky', 'weight' => 50],
            ['phrase' => 'blixky', 'weight' => 50],
            ['phrase' => 'stick with a switch', 'weight' => 60],
            ['phrase' => 'fully auto', 'weight' => 60],
            ['phrase' => 'semi auto', 'weight' => 40],
            ['phrase' => 'extendo', 'weight' => 50],
            ['phrase' => 'extended mag', 'weight' => 50],
            ['phrase' => 'drum', 'weight' => 50],
            ['phrase' => '30-rounder', 'weight' => 50],
            ['phrase' => '50-round drum', 'weight' => 60],
            ['phrase' => 'banana clip', 'weight' => 50],
            ['phrase' => 'hollow points', 'weight' => 60],
            ['phrase' => 'hp rounds', 'weight' => 50],
            ['phrase' => 'slugs', 'weight' => 40],
            ['phrase' => 'shells', 'weight' => 40],
            ['phrase' => 'pump', 'weight' => 40],
            ['phrase' => 'pump-action', 'weight' => 40],
            ['phrase' => 'sawed-off', 'weight' => 60],
            ['phrase' => 'gauge', 'weight' => 40],
            ['phrase' => '.223', 'weight' => 40],
            ['phrase' => '.556', 'weight' => 40],
            ['phrase' => 'rifle', 'weight' => 50],
            ['phrase' => 'gat', 'weight' => 50],
            ['phrase' => 'ratchet', 'weight' => 40],
            ['phrase' => 'toolie', 'weight' => 40],
            ['phrase' => 'tool', 'weight' => 40],
            ['phrase' => 'heat', 'weight' => 40],
            ['phrase' => 'hammer', 'weight' => 40],
            ['phrase' => 'glizzy', 'weight' => 40],
            ['phrase' => 'fn', 'weight' => 40],
            ['phrase' => 'sig', 'weight' => 40],
            ['phrase' => 'ruger', 'weight' => 40],
            ['phrase' => 'mossberg', 'weight' => 40],
            ['phrase' => 'xd', 'weight' => 40],
            ['phrase' => 'automatic', 'weight' => 40],
            ['phrase' => 'semi', 'weight' => 30],
            ['phrase' => 'revolver', 'weight' => 40],
            ['phrase' => 'pipe bomb', 'weight' => 80],
            ['phrase' => 'homemade bomb', 'weight' => 80],
            ['phrase' => 'molotov', 'weight' => 70],
            ['phrase' => 'blow something up', 'weight' => 70],
            ['phrase' => 'explode the building', 'weight' => 80],
            ['phrase' => 'incendiary', 'weight' => 70],
            ['phrase' => 'detonate', 'weight' => 70],
            ['phrase' => 'improvised device', 'weight' => 70],
            ['phrase' => 'ied', 'weight' => 70],
            ['phrase' => 'backpack bomb', 'weight' => 70],
            ['phrase' => 'time bomb', 'weight' => 70],
            ['phrase' => 'pressure cooker', 'weight' => 70],
            ['phrase' => 'ignition device', 'weight' => 70],
            ['phrase' => 'fuse', 'weight' => 50],
            ['phrase' => 'tnt', 'weight' => 70],
            ['phrase' => 'c4', 'weight' => 80],
            ['phrase' => 'plastique', 'weight' => 70],
            ['phrase' => 'charge', 'weight' => 50],
            ['phrase' => 'blast radius', 'weight' => 70],
            ['phrase' => 'kaboom', 'weight' => 60],
            ['phrase' => 'jump him', 'weight' => 40],
            ['phrase' => 'jumpout', 'weight' => 40],
            ['phrase' => 'pack out', 'weight' => 40],
            ['phrase' => 'stomp out', 'weight' => 50],
            ['phrase' => 'run fades', 'weight' => 40],
            ['phrase' => 'catch a fade', 'weight' => 40],
            ['phrase' => 'throw hands', 'weight' => 30],
            ['phrase' => 'knuckle up', 'weight' => 30],
            ['phrase' => 'smoke him', 'weight' => 50],
            ['phrase' => 'handle them', 'weight' => 40],
            ['phrase' => 'take him out', 'weight' => 60],
            ['phrase' => 'beatdown', 'weight' => 40],
            ['phrase' => 'curb stomp', 'weight' => 70],
            ['phrase' => 'fold him', 'weight' => 40],
            ['phrase' => 'press him', 'weight' => 40],
            ['phrase' => 'apply pressure', 'weight' => 40],
            ['phrase' => 'slide on them', 'weight' => 40],
            ['phrase' => 'spin back', 'weight' => 40],
            ['phrase' => 'spin the block', 'weight' => 50],
            ['phrase' => 'run down', 'weight' => 40],
            ['phrase' => 'run up', 'weight' => 40],
            ['phrase' => 'catch him lacking', 'weight' => 50],
            ['phrase' => 'caught lacking', 'weight' => 50],
            ['phrase' => 'line him up', 'weight' => 50],
            ['phrase' => 'green light', 'weight' => 50],
            ['phrase' => 'red light him', 'weight' => 50],
            ['phrase' => 'get dropped', 'weight' => 40],
            ['phrase' => 'body him', 'weight' => 50],
            ['phrase' => 'murk', 'weight' => 50],
            ['phrase' => 'finish him', 'weight' => 50],
            ['phrase' => 'opp', 'weight' => 40],
            ['phrase' => 'opps', 'weight' => 40],
            ['phrase' => 'opposition', 'weight' => 40],
            ['phrase' => 'slide on the opps', 'weight' => 50],
            ['phrase' => 'dead homies', 'weight' => 40],
            ['phrase' => 'gd', 'weight' => 40],
            ['phrase' => 'bd', 'weight' => 40],
            ['phrase' => 'crip', 'weight' => 40],
            ['phrase' => 'blood', 'weight' => 40],
            ['phrase' => 'vice lord', 'weight' => 40],
            ['phrase' => 'latin king', 'weight' => 40],
            ['phrase' => 'set trip', 'weight' => 40],
            ['phrase' => 'hood politics', 'weight' => 30],
            ['phrase' => 'gangin', 'weight' => 30],
            ['phrase' => 'banging', 'weight' => 30],
            ['phrase' => 'the set', 'weight' => 30],
            ['phrase' => 'on gang', 'weight' => 30],
            ['phrase' => 'on my dead homies', 'weight' => 30],
            ['phrase' => 'retaliation', 'weight' => 40],
            ['phrase' => 'payback', 'weight' => 40],
            ['phrase' => 'end my life', 'weight' => 80],
            ['phrase' => 'kill myself', 'weight' => 80],
            ['phrase' => 'kms', 'weight' => 70],
            ['phrase' => 'commit', 'weight' => 30],
            ['phrase' => 'self-harm', 'weight' => 70],
            ['phrase' => 'cut myself', 'weight' => 70],
            ['phrase' => "i don't want to live", 'weight' => 80],
            ['phrase' => 'disappear forever', 'weight' => 60],
            ['phrase' => 'nobody would miss me', 'weight' => 70],
            ['phrase' => 'going to sleep forever', 'weight' => 70],
            ['phrase' => 'overdose', 'weight' => 70],
            ['phrase' => 'od', 'weight' => 70],
            ['phrase' => 'take all my pills', 'weight' => 70],
            ['phrase' => "i'm done with life", 'weight' => 70],
            ['phrase' => 'no way out', 'weight' => 60],
            ['phrase' => 'suicidal', 'weight' => 80],
            ['phrase' => 'rope', 'weight' => 40],
            ['phrase' => 'hanging', 'weight' => 60],
            ['phrase' => 'bleed out', 'weight' => 70],
            ['phrase' => 'last message', 'weight' => 70],
        ];

        $orgSpecific = [
            ['phrase' => 'parking lot fight', 'weight' => 30],
            ['phrase' => 'back gate', 'weight' => 20],
            ['phrase' => 'slang-drill', 'weight' => 25],
        ];

        foreach ($global as $kw) {
            RiskKeyword::updateOrCreate(
                ['phrase' => strtolower($kw['phrase']), 'org_id' => null],
                ['weight' => $kw['weight']]
            );
        }

        foreach ($orgSpecific as $kw) {
            RiskKeyword::updateOrCreate(
                ['phrase' => strtolower($kw['phrase']), 'org_id' => $org->id],
                ['weight' => $kw['weight']]
            );
        }
    }

    protected function seedCms(Org $org): void
    {
        $pages = [
            [
                'title' => 'Schools',
                'slug' => 'schools',
                'content' => '<p>Internal safety, reporting, and alerting for schools.</p>',
                'meta_title' => 'Asylon for Schools',
                'meta_description' => 'Safety, reporting, and alerting for schools.',
            ],
            [
                'title' => 'Churches',
                'slug' => 'churches',
                'content' => '<p>Safety and incident alerting tailored for faith communities.</p>',
                'meta_title' => 'Asylon for Churches',
                'meta_description' => 'Safety and incident alerting for churches.',
            ],
            [
                'title' => 'Organizations',
                'slug' => 'organizations',
                'content' => '<p>Incident reporting and escalation for organizations.</p>',
                'meta_title' => 'Asylon for Organizations',
                'meta_description' => 'Incident reporting and escalation for organizations.',
            ],
        ];

        $pageMap = [];
        foreach ($pages as $pageData) {
            $page = Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                [
                    'title' => $pageData['title'],
                    'content' => $pageData['content'],
                    'meta_title' => $pageData['meta_title'],
                    'meta_description' => $pageData['meta_description'],
                    'published' => true,
                ],
            );
            $pageMap[$pageData['slug']] = $page;
        }

        // Bring in other statically-seeded pages if they exist (support/privacy/terms/submit-report)
        $additionalPages = Page::whereIn('slug', ['support', 'privacy', 'terms', 'submit-report'])->get();
        foreach ($additionalPages as $page) {
            $pageMap[$page->slug] = $page;
        }

        $headerMenu = Menu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Header']
        );

        $footerMenu = Menu::firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer']
        );

        $defaultHeaderItems = [
            ['title' => 'Schools', 'type' => 'page', 'page' => $pageMap['schools'] ?? null, 'url' => null, 'position' => 1],
            ['title' => 'Churches', 'type' => 'page', 'page' => $pageMap['churches'] ?? null, 'url' => null, 'position' => 2],
            ['title' => 'Organizations', 'type' => 'page', 'page' => $pageMap['organizations'] ?? null, 'url' => null, 'position' => 3],
            ['title' => 'Blog', 'type' => 'url', 'url' => '/blog', 'position' => 4],
            ['title' => 'Submit a Report', 'type' => 'url', 'url' => '/report', 'position' => 5],
        ];

        foreach ($defaultHeaderItems as $item) {
            $pageId = ($item['type'] === 'page' && isset($item['page'])) ? ($item['page']?->id ?? null) : null;
            MenuItem::updateOrCreate(
                [
                    'menu_id' => $headerMenu->id,
                    'title' => $item['title'],
                ],
                [
                    'type' => $item['type'],
                    'page_id' => $pageId,
                    'url' => $item['type'] === 'url' ? ($item['url'] ?? null) : null,
                    'position' => $item['position'],
                    'target' => '_self',
                ],
            );
        }

        $defaultFooterItems = [
            ['title' => 'Schools', 'type' => 'page', 'page' => $pageMap['schools'] ?? null, 'url' => null, 'position' => 1],
            ['title' => 'Churches', 'type' => 'page', 'page' => $pageMap['churches'] ?? null, 'url' => null, 'position' => 2],
            ['title' => 'Organizations', 'type' => 'page', 'page' => $pageMap['organizations'] ?? null, 'url' => null, 'position' => 3],
            ['title' => 'Submit a Report', 'type' => 'url', 'url' => '/report', 'position' => 4],
            ['title' => 'Blog', 'type' => 'url', 'url' => '/blog', 'position' => 5],
            ['title' => 'Support', 'type' => 'page', 'page' => $pageMap['support'] ?? null, 'url' => '/support', 'position' => 6],
            ['title' => 'Privacy', 'type' => 'page', 'page' => $pageMap['privacy'] ?? null, 'url' => '/privacy', 'position' => 7],
            ['title' => 'Terms', 'type' => 'page', 'page' => $pageMap['terms'] ?? null, 'url' => '/terms', 'position' => 8],
        ];

        foreach ($defaultFooterItems as $item) {
            $pageId = ($item['type'] === 'page' && isset($item['page'])) ? ($item['page']?->id ?? null) : null;
            MenuItem::updateOrCreate(
                [
                    'menu_id' => $footerMenu->id,
                    'title' => $item['title'],
                ],
                [
                    'type' => $item['type'] ?? 'url',
                    'page_id' => $pageId,
                    'url' => $item['type'] === 'url' ? ($item['url'] ?? null) : null,
                    'position' => $item['position'],
                    'target' => '_self',
                ],
            );
        }
    }

    protected function seedSeoPages(): void
    {
        $slugs = [
            'home',
            'how-it-works',
            'solutions-schools',
            'solutions-churches',
            'solutions-organizations',
            'features',
            'resources',
            'about',
            'book-a-demo',
            'contact',
            'blog',
        ];

        foreach ($slugs as $slug) {
            SeoPage::firstOrCreate(['slug' => $slug], [
                'meta_title' => ucwords(str_replace('-', ' ', $slug)).' | '.config('app.name', 'Asylon'),
                'meta_description' => 'SEO settings placeholder for '.$slug,
            ]);
        }
    }
}


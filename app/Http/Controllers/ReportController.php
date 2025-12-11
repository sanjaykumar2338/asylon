<?php

namespace App\Http\Controllers;

use App\Events\ReportSubmitted;
use App\Http\Requests\StoreEmployeeReportRequest;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\StoreStudentReportRequest;
use App\Models\Org;
use App\Models\OrgAlertContact;
use App\Models\ReportCategory;
use App\Models\Report;
use App\Models\User;
use App\Models\Page;
use App\Jobs\AnonymizeVoiceJob;
use App\Jobs\AnalyzeReportRisk;
use App\Jobs\AnalyzeThreatAssessment;
use App\Jobs\TranscribeAudioJob;
use App\Notifications\ReportAlertNotification;
use App\Services\Audit;
use App\Services\AttachmentSafetyScanner;
use App\Support\LocaleManager;
use App\Support\ReportLinkGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Show the anonymous report submission form.
     */
    public function create(): View
    {
        $orgs = $this->loadActiveOrgs();
        $categories = $this->loadCategories();
        [$portalHeading, $portalDescription, $submitPage] = $this->submitPageContent(__('report.submit_title'), __('report.submit_description'));

        return view('report.create', [
            'orgs' => $orgs,
            'categories' => $categories,
            'lockedOrg' => null,
            'types' => $this->availableTypesFor(null),
            'orgTypeMap' => $this->mapOrgTypes($orgs),
            'typeCategoryMap' => $this->typeCategoryMap(),
            'hrCategories' => config('asylon.reports.hr_category_map', []),
            'portalSource' => 'general',
            'formAction' => route('report.store'),
            'showTypeSelector' => true,
            'forceType' => null,
            'portalHeading' => $portalHeading,
            'portalDescription' => $portalDescription,
            'submitPage' => $submitPage,
            'recipientsEnabled' => false,
            'recipientMap' => [],
        ]);
    }

    /**
     * Show the report form for a specific organization code.
     */
    public function createByCode(string $org_code): View
    {
        $org = Org::where('org_code', $org_code)->firstOrFail();
        if ($org->billing_status !== 'active') {
            return $this->portalDisabled('general');
        }
        LocaleManager::applyOrgLocale($org);
        [$portalHeading, $portalDescription, $submitPage] = $this->submitPageContent(__('report.submit_title'), __('report.submit_description'));

        return view('report.create', [
            'orgs' => null,
            'categories' => $this->loadCategories(),
            'lockedOrg' => $org,
            'types' => $this->availableTypesFor($org),
            'orgTypeMap' => [],
            'typeCategoryMap' => $this->typeCategoryMap(),
            'hrCategories' => config('asylon.reports.hr_category_map', []),
            'portalSource' => 'general',
            'formAction' => route('report.store'),
            'showTypeSelector' => true,
            'forceType' => null,
            'portalHeading' => $portalHeading,
            'portalDescription' => $portalDescription,
            'submitPage' => $submitPage,
            'recipientsEnabled' => false,
            'recipientMap' => [],
        ]);
    }

    /**
     * Public portal for student safety submissions.
     */
    public function createStudent(): View
    {
        $orgs = $this->loadActiveOrgs()
            ->filter(fn (Org $org) => $org->enable_student_reports)
            ->values();

        if ($orgs->isEmpty()) {
            return $this->portalDisabled('student');
        }

        $categories = $this->loadCategoriesByTypes(['student', 'both']);
        [$portalHeading, $portalDescription, $submitPage] = $this->submitPageContent(__('report.student_portal_heading'), __('report.student_portal_description'));

        return view('report.create', [
            'orgs' => $orgs,
            'categories' => $categories,
            'lockedOrg' => null,
            'types' => ['safety' => __('Safety & Threat')],
            'orgTypeMap' => $this->mapOrgTypes($orgs),
            'typeCategoryMap' => $this->typeCategoryMap(),
            'hrCategories' => config('asylon.reports.hr_category_map', []),
            'portalSource' => 'student',
            'formAction' => route('report.student.store'),
            'showTypeSelector' => false,
            'forceType' => 'safety',
            'portalHeading' => $portalHeading,
            'portalDescription' => $portalDescription,
            'submitPage' => $submitPage,
            'recipientsEnabled' => false,
            'recipientMap' => [],
        ]);
    }

    /**
     * Public portal for employee HR / commendation submissions.
     */
    public function createEmployee(): View
    {
        $orgs = $this->loadActiveOrgs()
            ->filter(fn (Org $org) => ($org->enable_hr_reports || $org->enable_commendations))
            ->values();

        if ($orgs->isEmpty()) {
            return $this->portalDisabled('employee');
        }

        $categories = $this->loadCategoriesByTypes(['employee', 'both']);
        $recipientMap = $this->recipientMap(
            config('asylon.alerts.employee_departments', ['hr', 'ethics', 'admin'])
        );
        [$portalHeading, $portalDescription, $submitPage] = $this->submitPageContent(__('report.employee_portal_heading'), __('report.employee_portal_description'));

        return view('report.create', [
            'orgs' => $orgs,
            'categories' => $categories,
            'lockedOrg' => null,
            'types' => [
                'hr' => __('HR Anonymous'),
                'commendation' => __('Commendation'),
            ],
            'orgTypeMap' => $this->mapOrgTypes($orgs),
            'typeCategoryMap' => $this->typeCategoryMap(),
            'hrCategories' => config('asylon.reports.hr_category_map', []),
            'portalSource' => 'employee',
            'formAction' => route('report.employee.store'),
            'showTypeSelector' => true,
            'forceType' => null,
            'portalHeading' => $portalHeading,
            'portalDescription' => $portalDescription,
            'submitPage' => $submitPage,
            'recipientsEnabled' => true,
            'recipientMap' => $recipientMap,
        ]);
    }

    /**
     * Store a newly created report.
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $report = $this->persistReport($request, [
            'portal_source' => 'general',
        ]);

        $isUltraPrivate = $this->usesUltraPrivateMode($request, $report->org);
        event(new ReportSubmitted($report, $this->dashboardBaseUrl($request)));
        $this->logPortalSubmission($report, $isUltraPrivate);
        $this->notifyReviewersAboutReport($report);
        AnalyzeReportRisk::dispatch($report);

        return Redirect::route('report.thanks', $report->getKey());
    }

    /**
     * Store a student-portal report.
     */
    public function storeStudent(StoreStudentReportRequest $request): RedirectResponse
    {
        $report = $this->persistReport($request, [
            'portal_source' => 'student',
            'type' => 'safety',
        ]);

        $isUltraPrivate = $this->usesUltraPrivateMode($request, $report->org);
        event(new ReportSubmitted($report, $this->dashboardBaseUrl($request)));
        $this->logPortalSubmission($report, $isUltraPrivate);
        $this->notifyReviewersAboutReport($report);
        AnalyzeReportRisk::dispatch($report);

        return Redirect::route('report.thanks', $report->getKey());
    }

    /**
     * Store an employee-portal report.
     */
    public function storeEmployee(StoreEmployeeReportRequest $request): RedirectResponse
    {
        $recipients = $this->resolveEmployeeRecipients(
            (int) $request->input('org_id'),
            $request->input('recipients', [])
        );

        if ($recipients->isEmpty()) {
            throw ValidationException::withMessages([
                'recipients' => __('report.errors.recipient_required'),
            ]);
        }

        $report = $this->persistReport($request, [
            'portal_source' => 'employee',
            'meta' => [
                'recipients' => $recipients->pluck('id')->all(),
            ],
        ]);

        $isUltraPrivate = $this->usesUltraPrivateMode($request, $report->org);
        event(new ReportSubmitted($report, $this->dashboardBaseUrl($request)));
        $this->logPortalSubmission($report, $isUltraPrivate);
        $this->notifyReviewersAboutReport($report);
        AnalyzeReportRisk::dispatch($report);

        return Redirect::route('report.thanks', $report->getKey());
    }

    /**
     * Display the thank you page once a report is submitted.
     */
    public function thanks(string $id): View
    {
        $report = Report::with('org')->findOrFail($id);
        LocaleManager::applyOrgLocale($report->org);

        return view('report.thanks', [
            'id' => $report->getKey(),
            'report' => $report,
            'followupUrl' => $report->chat_token
                ? ReportLinkGenerator::followup($report)
                : null,
        ]);
    }

    /**
     * Render a branded disabled-portal page instead of a generic 403.
     */
    protected function portalDisabled(string $portal): Response
    {
        $title = __('Reporting portal disabled');
        $message = __('This reporting portal is disabled for your organization. Please contact your administrator.');

        $meta = match ($portal) {
            'student' => [
                'heading' => __('Student Reporting Unavailable'),
                'subheading' => __('This link is currently turned off.'),
            ],
            'employee' => [
                'heading' => __('HR / Employee Reporting Unavailable'),
                'subheading' => __('This link is currently turned off.'),
            ],
            default => [
                'heading' => $title,
                'subheading' => null,
            ],
        };

        return response()
            ->view('report.disabled', [
                'title' => $title,
                'message' => $message,
                'heading' => $meta['heading'] ?? $title,
                'subheading' => $meta['subheading'] ?? null,
            ], 403);
    }

    /**
     * Fetch active organizations allowed for public reporting.
     */
    protected function loadActiveOrgs()
    {
        return Org::query()
            ->where('status', 'active')
            ->where('billing_status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'enable_commendations', 'enable_hr_reports', 'enable_student_reports']);
    }

    /**
     * Load available categories and subcategories for the report form.
     *
     * @return array<string, array<int, string>>
     */
    protected function loadCategories(): array
    {
        return $this->loadCategoriesByTypes(['student', 'employee', 'both']);
    }

    /**
     * Load categories filtered by their type flag.
     *
     * @param  array<int, string>  $types
     * @return array<string, array<int, string>>
     */
    protected function loadCategoriesByTypes(array $types): array
    {
        if (! in_array('both', $types, true)) {
            $types[] = 'both';
        }

        return ReportCategory::query()
            ->visible()
            ->with(['subcategories' => fn ($query) => $query->orderBy('position')->orderBy('name')])
            ->whereIn('type', $types)
            ->orderBy('position')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(function (ReportCategory $category): array {
                $subcategories = $category->subcategories
                    ->map(fn ($sub) => $sub->name)
                    ->all();

                return [$category->name => $subcategories];
            })
            ->toArray();
    }

    /**
     * Persist a report and store attachments.
     *
     * @param  array<string, mixed>  $overrides
     */
    protected function persistReport(StoreReportRequest $request, array $overrides = []): Report
    {
        return DB::transaction(function () use ($request, $overrides): Report {
            $validated = array_merge($request->validated(), $overrides);
            $attachments = $validated['attachments'] ?? [];
            $voiceComment = $validated['voice_comment'] ?? null;
            $org = null;
            $safetyScanner = new AttachmentSafetyScanner();
            $reporterFlaggedSensitive = $request->boolean('attachment_may_contain_sensitive_content');

            if ($request->filled('org_code')) {
                $orgCode = trim((string) $request->input('org_code'));
                $org = Org::where('org_code', $orgCode)->firstOrFail();
                if ($org->billing_status !== 'active') {
                    abort(403, 'Reporting is disabled for this organization.');
                }
                $validated['org_id'] = $org->id;
            }

            if (! $org) {
                $orgId = $validated['org_id'] ?? null;
                $org = $orgId ? Org::findOrFail($orgId) : null;
                if ($org && $org->billing_status !== 'active') {
                    abort(403, 'Reporting is disabled for this organization.');
                }
            }

            $types = $org ? array_keys($org->enabledTypes()) : array_keys($this->defaultTypeOptions());

            if (! in_array($validated['type'], $types, true)) {
                abort(422, 'This report type is not enabled for the selected organization.');
            }

            if (! $this->categoryAllowedForType($validated['type'], $validated['category'])) {
                abort(422, 'Please select a category that matches the chosen report type.');
            }

            unset($validated['attachments'], $validated['voice_comment'], $validated['org_code']);

            $validated = $this->applyPrivacyMetadata($request, $validated, $org);

            if (isset($validated['meta']) && empty($validated['meta'])) {
                $validated['meta'] = null;
            }

            if (empty($validated['severity'])) {
                $validated['severity'] = 'moderate';
            }

            $report = Report::create($validated);
            $report->chat_token = (string) Str::uuid();
            $report->save();
            AnalyzeThreatAssessment::dispatch($report->getKey());

            foreach ($attachments as $index => $attachment) {
                $file = $request->file("attachments.$index.file");

                if (! $file) {
                    continue;
                }

                $comment = isset($attachment['comment']) ? trim((string) $attachment['comment']) : '';
                $comment = $comment === '' ? null : $comment;
                $storedPath = $file->store("reports/{$report->getKey()}", 'public');

                $safetyMeta = $this->scanAttachment($safetyScanner, $file);

                $reportFile = $report->files()->create([
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'comment' => $comment,
                    'safety_scan_status' => $safetyMeta['status'],
                    'safety_scan_reasons' => $safetyMeta['reasons'],
                    'has_sensitive_content' => $reporterFlaggedSensitive || $safetyMeta['sensitive'],
                ]);

                if ($reportFile->isAudio()) {
                    AnonymizeVoiceJob::dispatch($reportFile);
                    TranscribeAudioJob::dispatch($reportFile);
                }
            }

            $voiceFile = $request->file('voice_recording');
            if ($voiceFile) {
                $normalizedVoiceComment = is_string($voiceComment) ? trim($voiceComment) : '';
                $normalizedVoiceComment = $normalizedVoiceComment === '' ? null : $normalizedVoiceComment;
                $storedPath = $voiceFile->store("reports/{$report->getKey()}", 'public');

                $safetyMeta = $this->scanAttachment($safetyScanner, $voiceFile);

                $voiceReportFile = $report->files()->create([
                    'path' => $storedPath,
                    'original_name' => $voiceFile->getClientOriginalName(),
                    'mime' => $voiceFile->getMimeType(),
                    'size' => $voiceFile->getSize(),
                    'comment' => $normalizedVoiceComment,
                    'safety_scan_status' => $safetyMeta['status'],
                    'safety_scan_reasons' => $safetyMeta['reasons'],
                    'has_sensitive_content' => $reporterFlaggedSensitive || $safetyMeta['sensitive'],
                ]);

                if ($voiceReportFile->isAudio()) {
                    AnonymizeVoiceJob::dispatch($voiceReportFile);
                    TranscribeAudioJob::dispatch($voiceReportFile);
                }
            }

            return $report;
        });
    }

    /**
     * Attach privacy metadata when ultra-private mode is enabled.
     */
    protected function applyPrivacyMetadata(StoreReportRequest $request, array $validated, ?Org $org): array
    {
        if (! $this->usesUltraPrivateMode($request, $org)) {
            return $validated;
        }

        $existingMeta = [];
        if (isset($validated['meta']) && is_array($validated['meta'])) {
            $existingMeta = $validated['meta'];
        }

        $privacyMeta = array_filter([
            'ultra_private' => true,
            'subpoena_token' => $request->attributes->get('privacy.subpoena_hash'),
        ], static fn ($value) => $value !== null && $value !== '');

        $validated['meta'] = array_filter([
            ...$existingMeta,
            'privacy' => $privacyMeta,
        ]);

        return $validated;
    }

    /**
     * Persist an audit log for a portal submission.
     */
    protected function logPortalSubmission(Report $report, bool $ultraPrivate = false): void
    {
        $meta = [
            'portal_source' => $report->portal_source,
            'type' => $report->type,
        ];

        if ($ultraPrivate) {
            $privacyMeta = $report->meta['privacy'] ?? [];
            $meta['privacy_mode'] = 'ultra_private';
            $meta['privacy_token_present'] = ! empty($privacyMeta['subpoena_token']);
        }

        Audit::log('system', 'portal_submission', 'report', $report->getKey(), $meta);
    }

    /**
     * Determine whether the incoming request should be handled in ultra-private mode.
     */
    protected function usesUltraPrivateMode(StoreReportRequest $request, ?Org $org): bool
    {
        if ($request->attributes->get('privacy.ultra_private') === true) {
            return true;
        }

        return (bool) ($org?->enable_ultra_private_mode ?? config('asylon.ultra_private_mode', false));
    }

    /**
     * Determine the absolute dashboard base URL to use in alerts.
     */
    protected function dashboardBaseUrl(StoreReportRequest $request): string
    {
        $root = trim((string) ($request->root() ?: url()->to('/')));

        return $root === '' ? url()->to('/') : rtrim($root, '/');
    }

    /**
     * Load optional page-managed content for the submit portal.
     */
    protected function submitPageContent(string $fallbackHeading, string $fallbackDescription): array
    {
        $page = Page::where('slug', 'submit-report')->where('published', true)->first();

        if (! $page) {
            return [$fallbackHeading, $fallbackDescription, null];
        }

        $heading = $page->title ?: $fallbackHeading;
        $description = $page->excerpt ?: $fallbackDescription;

        return [$heading, $description, $page];
    }

    /**
     * Run a lightweight safety scan and normalize the response.
     */
    protected function scanAttachment(AttachmentSafetyScanner $scanner, UploadedFile $file): array
    {
        $result = $scanner->evaluate($file);

        return [
            'status' => $result['status'] ?? 'pending_review',
            'reasons' => $result['reasons'] ?? [],
            'sensitive' => (bool) ($result['sensitive'] ?? false),
        ];
    }

    /**
     * Resolve employee recipients constrained to allowed departments.
     *
     * @param  array<int, int|string>  $candidateIds
     */
    protected function resolveEmployeeRecipients(int $orgId, array $candidateIds)
    {
        if ($orgId === 0 || empty($candidateIds)) {
            return collect();
        }

        $departments = config('asylon.alerts.employee_departments', []);

        $query = OrgAlertContact::query()
            ->where('org_id', $orgId)
            ->where('is_active', true)
            ->whereIn('id', $candidateIds);

        if (! empty($departments)) {
            $query->whereIn('department', $departments);
        }

        return $query->get();
    }

    /**
     * Build a map of orgs to their eligible recipients.
     *
     * @param  array<int, string>  $departments
     * @return array<int, array<int, array<string, mixed>>>
     */
    protected function recipientMap(array $departments): array
    {
        if (empty($departments)) {
            return [];
        }

        return Org::query()
            ->where('status', 'active')
            ->with(['alertContacts' => function ($query) use ($departments): void {
                $query->where('is_active', true)->whereIn('department', $departments);
            }])
            ->get(['id', 'name'])
            ->mapWithKeys(function (Org $org): array {
                $recipients = $org->alertContacts
                    ->map(function (OrgAlertContact $contact): array {
                        return [
                            'id' => $contact->id,
                            'value' => $contact->value,
                            'department' => $contact->department,
                            'type' => $contact->type,
                        ];
                    })
                    ->values();

                return [$org->id => $recipients];
            })
            ->toArray();
    }

    /**
     * Determine which report types are available for the organization.
     *
     * @return array<string, string>
     */
    protected function availableTypesFor(?Org $org): array
    {
        if ($org) {
            $types = $org->enabledTypes();

            return $types;
        }

        return $this->defaultTypeOptions();
    }

    /**
     * Default type options when no org constraints are available.
     *
     * @return array<string, string>
     */
    protected function defaultTypeOptions(): array
    {
        return [
            'safety' => __('Safety & Threat'),
            'commendation' => __('Commendation'),
            'hr' => __('HR Anonymous'),
        ];
    }

    /**
     * Map of report types to the categories they expose.
     *
     * @return array<string, array<int, string>>
     */
    protected function typeCategoryMap(): array
    {
        return config('asylon.reports.type_categories', []);
    }

    /**
     * Determine if the selected category is allowed for the type.
     */
    protected function categoryAllowedForType(string $type, string $category): bool
    {
        $map = $this->typeCategoryMap();
        $allowed = $map[$type] ?? [];

        if (! is_array($allowed) || $allowed === [] || in_array('all', $allowed, true)) {
            return true;
        }

        return in_array($category, $allowed, true);
    }

    /**
     * Build a map of org IDs to allowed type keys for the form.
     */
    protected function mapOrgTypes($orgs): array
    {
        if (! $orgs) {
            return [];
        }

        return $orgs->mapWithKeys(function (Org $org): array {
            return [$org->id => array_keys($org->enabledTypes())];
        })->all();
    }

    /**
     * Notify reviewers and admins through database notifications.
     */
    protected function notifyReviewersAboutReport(Report $report): void
    {
        $report->loadMissing('org');

        $roles = ['reviewer', 'security_lead', 'org_admin', 'platform_admin'];

        $recipients = User::query()
            ->where('active', true)
            ->whereIn('role', $roles)
            ->where(function ($query) use ($report): void {
                $query->whereNull('org_id')
                    ->orWhere('org_id', $report->org_id);
            })
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        $orgName = $report->org?->name ?: __('General submission');

        Notification::send(
            $recipients,
            new ReportAlertNotification(
                title: __('New report submitted'),
                message: __('Report #:id (:type) for :org requires review.', [
                    'id' => $report->getKey(),
                    'type' => ucfirst($report->type ?? 'general'),
                    'org' => $orgName,
                ]),
                url: route('reports.show', $report),
            )
        );
    }
}

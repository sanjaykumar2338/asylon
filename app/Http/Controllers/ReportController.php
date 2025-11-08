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
use App\Services\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Show the anonymous report submission form.
     */
    public function create(): View
    {
        $orgs = $this->loadActiveOrgs();
        $categories = $this->loadCategories();

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
            'portalHeading' => __('Submit a Security Concern'),
            'portalDescription' => __('Use this form to anonymously report a security issue or concern. Only the reviewing team for your organization will be able to access the information you provide.'),
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
            'portalHeading' => __('Submit a Security Concern'),
            'portalDescription' => __('Use this form to anonymously report a security issue or concern. Only the reviewing team for your organization will be able to access the information you provide.'),
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
            abort(403, 'Student reporting is disabled.');
        }

        $categories = $this->loadCategoriesByTypes(['student', 'both']);

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
            'portalHeading' => __('Student Safety & Well-being'),
            'portalDescription' => __('This portal is for student safety, bullying, discrimination, or wellness concerns.'),
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
            abort(403, 'Employee reporting is disabled.');
        }

        $categories = $this->loadCategoriesByTypes(['employee', 'both']);
        $recipientMap = $this->recipientMap(
            config('asylon.alerts.employee_departments', ['hr', 'ethics', 'admin'])
        );

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
            'portalHeading' => __('Employee Anonymous Reporting'),
            'portalDescription' => __('Use this portal for HR concerns, workplace issues, or commendations. Select who should be notified below.'),
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

        event(new ReportSubmitted($report));
        $this->logPortalSubmission($report);

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

        event(new ReportSubmitted($report));
        $this->logPortalSubmission($report);

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
                'recipients' => __('Select at least one eligible recipient.'),
            ]);
        }

        $report = $this->persistReport($request, [
            'portal_source' => 'employee',
            'meta' => [
                'recipients' => $recipients->pluck('id')->all(),
            ],
        ]);

        event(new ReportSubmitted($report));
        $this->logPortalSubmission($report);

        return Redirect::route('report.thanks', $report->getKey());
    }

    /**
     * Display the thank you page once a report is submitted.
     */
    public function thanks(string $id): View
    {
        $report = Report::findOrFail($id);

        return view('report.thanks', [
            'id' => $id,
            'chatToken' => $report->chat_token,
        ]);
    }

    /**
     * Fetch active organizations allowed for public reporting.
     */
    protected function loadActiveOrgs()
    {
        return Org::query()
            ->where('status', 'active')
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

            if ($request->filled('org_code')) {
                $orgCode = trim((string) $request->input('org_code'));
                $org = Org::where('org_code', $orgCode)->firstOrFail();
                $validated['org_id'] = $org->id;
            }

            if (! $org) {
                $orgId = $validated['org_id'] ?? null;
                $org = $orgId ? Org::findOrFail($orgId) : null;
            }

            $types = $org ? array_keys($org->enabledTypes()) : array_keys($this->defaultTypeOptions());

            if (! in_array($validated['type'], $types, true)) {
                abort(422, 'This report type is not enabled for the selected organization.');
            }

            if (! $this->categoryAllowedForType($validated['type'], $validated['category'])) {
                abort(422, 'Please select a category that matches the chosen report type.');
            }

            unset($validated['attachments'], $validated['voice_comment'], $validated['org_code']);

            if (isset($validated['meta']) && empty($validated['meta'])) {
                $validated['meta'] = null;
            }

            $report = Report::create($validated);
            $report->chat_token = (string) Str::uuid();
            $report->save();

            foreach ($attachments as $index => $attachment) {
                $file = $request->file("attachments.$index.file");

                if (! $file) {
                    continue;
                }

                $comment = isset($attachment['comment']) ? trim((string) $attachment['comment']) : '';
                $comment = $comment === '' ? null : $comment;
                $storedPath = $file->store("reports/{$report->getKey()}", 'public');

                $report->files()->create([
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'comment' => $comment,
                ]);
            }

            $voiceFile = $request->file('voice_recording');
            if ($voiceFile) {
                $normalizedVoiceComment = is_string($voiceComment) ? trim($voiceComment) : '';
                $normalizedVoiceComment = $normalizedVoiceComment === '' ? null : $normalizedVoiceComment;
                $storedPath = $voiceFile->store("reports/{$report->getKey()}", 'public');

                $report->files()->create([
                    'path' => $storedPath,
                    'original_name' => $voiceFile->getClientOriginalName(),
                    'mime' => $voiceFile->getMimeType(),
                    'size' => $voiceFile->getSize(),
                    'comment' => $normalizedVoiceComment,
                ]);
            }

            return $report;
        });
    }

    /**
     * Persist an audit log for a portal submission.
     */
    protected function logPortalSubmission(Report $report): void
    {
        Audit::log('system', 'portal_submission', 'report', $report->getKey(), [
            'portal_source' => $report->portal_source,
            'type' => $report->type,
        ]);
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
}

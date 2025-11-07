<?php

namespace App\Http\Controllers;

use App\Events\ReportSubmitted;
use App\Http\Requests\StoreReportRequest;
use App\Models\Org;
use App\Models\ReportCategory;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
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
        ]);
    }

    /**
     * Store a newly created report.
     */
    public function store(StoreReportRequest $request): RedirectResponse
    {
        $report = DB::transaction(function () use ($request): Report {
            $validated = $request->validated();
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

            if ($org) {
                $types = array_keys($org->enabledTypes());
            } else {
                $types = array_keys($this->defaultTypeOptions());
            }

            if (! in_array($validated['type'], $types, true)) {
                abort(422, 'This report type is not enabled for the selected organization.');
            }

            unset($validated['attachments']);
            unset($validated['voice_comment']);
            unset($validated['org_code']);

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

        event(new ReportSubmitted($report));

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
            ->get(['id', 'name', 'enable_commendations', 'enable_hr_reports']);
    }

    /**
     * Load available categories and subcategories for the report form.
     *
     * @return array<string, array<int, string>>
     */
    protected function loadCategories(): array
    {
        return ReportCategory::query()
            ->with(['subcategories'])
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
     * Determine which report types are available for the organization.
     *
     * @return array<string, string>
     */
    protected function availableTypesFor(?Org $org): array
    {
        if ($org) {
            $types = $org->enabledTypes();

            return $types === [] ? $this->defaultTypeOptions() : $types;
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

<x-guest-layout>
    @php
        $portalSource = $portalSource ?? 'general';
        $formAction = $formAction ?? route('report.store');
        $showTypeSelector = $showTypeSelector ?? true;
        $forceType = $forceType ?? null;
        $portalHeading = $portalHeading ?? __('report.submit_title');
        $portalDescription = $portalDescription ?? __('report.submit_description');
        $recipientsEnabled = $recipientsEnabled ?? false;
        $recipientMap = $recipientMap ?? [];
        $supportEmail = config('asylon.support_email', 'support@asylon.cc');
        $infoEmail = config('asylon.info_email', 'info@asylon.cc');
    @endphp
    <style>
        @media (min-width: 640px) {
            .sm\:max-w-md {
                max-width: 30rem;
            }
        }
    </style>
    <header class="mb-8 border-b border-gray-200 pb-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <a href="{{ url('/') }}" class="text-sm text-indigo-600 hover:underline">{{ config('app.name', 'Asylon') }}</a>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 px-3 py-2 font-semibold text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-sign-in-alt mr-2"></i> {{ __('Log In') }}
                </a>
            </div>
        </div>
    </header>

    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $portalHeading }}</h1>
        @if(isset($submitPage) && $submitPage)
            @if($portalDescription)
                <p class="mt-2 text-sm text-gray-700">{{ $portalDescription }}</p>
            @endif
            <div class="mt-3 space-y-3 prose prose-indigo max-w-none text-sm text-gray-700">
                {!! $submitPage->content !!}
            </div>
        @else
            <div class="mt-2 space-y-3 text-sm text-gray-700">
                <p>You stay anonymous unless YOU choose to share your information.<br>
                    Your identity is completely protected.</p>

                <p>Your voice matters.<br>
                    Use this form to report a concern, share information, or speak up about something that doesn't feel right.<br>
                    You may remain anonymous if you prefer.</p>

                <p>If this is an emergency, please contact 911 immediately.</p>
            </div>
            @if ($portalDescription && $portalDescription !== __('report.submit_description'))
                <p class="mt-3 text-sm text-gray-600">
                    {{ $portalDescription }}
                </p>
            @endif
        @endif
        <p class="text-sm text-gray-600 mt-4">
            {{ __('report.already_have_case') }}
            <a href="{{ route('followup.entry') }}" class="text-indigo-600 underline">
                {{ __('report.followup_cta') }}
            </a>.
        </p>
        <p class="mt-3 text-sm text-indigo-700">
            <a href="{{ route('privacy.anonymity') }}" class="underline">Learn how privacy &amp; anonymity work</a>
            &middot;
            <a href="{{ route('security.overview') }}" class="underline">Security overview</a>
        </p>
        <p class="mt-3 text-sm font-medium text-indigo-700">
            {{ __('report.privacy_header') }}
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <strong class="block font-semibold">{{ __('report.errors.title') }}</strong>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $hasCategories = ! empty($categories);
        $selectedCategory = $hasCategories ? old('category') : null;
        $initialSubcategories = $selectedCategory && isset($categories[$selectedCategory])
            ? $categories[$selectedCategory]
            : [];
    @endphp

    @unless ($hasCategories)
        <div class="mb-4 rounded-md border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-700">
            {{ __('report.no_categories') }}
        </div>
    @endunless

    <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        @if (isset($lockedOrg))
            <input type="hidden" name="org_code" value="{{ old('org_code', $lockedOrg->org_code) }}">
            <div class="rounded-md border border-indigo-200 bg-indigo-50 p-4 text-sm text-indigo-700">
                {{ __('report.reporting_to') }} <strong>{{ $lockedOrg->name }}</strong>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('org_code')" />
        @else
            <div>
                <x-input-label for="org_id" :value="__('report.organization_label')" />
                <select id="org_id" name="org_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('report.organization_placeholder') }}</option>
            @foreach ($orgs as $org)
                <option value="{{ $org->id }}" @selected(old('org_id') == $org->id)>{{ $org->name }}</option>
            @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('org_id')" />
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                @if ($showTypeSelector)
                    <x-input-label for="type" :value="__('report.type_label')" />
                    <select id="type" name="type"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        required>
                        @foreach (($types ?? []) as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $forceType ?? 'safety') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @else
                    <input type="hidden" name="type" value="{{ $forceType ?? 'safety' }}">
                    <x-input-label :value="__('report.type_label')" />
                    <div class="mt-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ ($types[$forceType ?? 'safety'] ?? ucfirst($forceType ?? 'safety')) }}
                    </div>
                @endif
                <x-input-error class="mt-2" :messages="$errors->get('type')" />
            </div>
            <div>
                <x-input-label :value="__('report.severity_label')" />
                <div class="mt-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                    {{ __('Automatically assigned after submission') }}
                </div>
            </div>
        </div>

        <div>
            <x-input-label for="category" :value="__('report.category_label')" />
            <select id="category" name="category"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                {{ $hasCategories ? '' : 'disabled' }} required>
                <option value="">{{ __('report.category_placeholder') }}</option>
                @foreach ($categories as $categoryName => $subcategoryList)
                    <option value="{{ $categoryName }}" @selected($selectedCategory === $categoryName)>{{ $categoryName }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('category')" />
        </div>

        <div>
            <x-input-label for="subcategory" :value="__('report.subcategory_label')" />
            <select id="subcategory" name="subcategory"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                {{ $hasCategories && $selectedCategory ? '' : 'disabled' }} required>
                <option value="">{{ __('report.subcategory_placeholder') }}</option>
                @foreach ($initialSubcategories as $subcategory)
                    <option value="{{ $subcategory }}" @selected(old('subcategory') === $subcategory)>{{ $subcategory }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('subcategory')" />
        </div>

        <div>
            <x-input-label for="description" :value="__('report.description_label')" />
            <textarea id="description" name="description" rows="6" required
                placeholder="Please describe what happened or what you've noticed.&#10;(Share as much as you feel comfortable.)"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            <small class="mt-2 block text-xs text-gray-500">
                Please describe what happened or what you've noticed. Share as much as you feel comfortable.
            </small>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div>
            <x-input-label for="violation_date" :value="__('report.violation_date_label')" />
            <x-text-input id="violation_date" name="violation_date" type="date"
                class="mt-1 block w-full" value="{{ old('violation_date') }}" />
            <x-input-error class="mt-2" :messages="$errors->get('violation_date')" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="contact_name" :value="__('report.contact_name_label')" />
                <x-text-input id="contact_name" name="contact_name" type="text" class="mt-1 block w-full"
                    value="{{ old('contact_name') }}" maxlength="150" />
                <p class="mt-1 text-xs text-gray-500">{{ __('report.contact_hint') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('contact_name')" />
            </div>

            <div>
                <x-input-label for="contact_email" :value="__('report.contact_email_label')" />
                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full"
                    value="{{ old('contact_email') }}" />
                <p class="mt-1 text-xs text-gray-500">{{ __('report.contact_hint') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
            </div>

            <div>
                <x-input-label for="contact_phone" :value="__('report.contact_phone_label')" />
                <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full"
                    value="{{ old('contact_phone') }}" maxlength="30" />
                <p class="mt-1 text-xs text-gray-500">{{ __('report.contact_hint') }}</p>
                <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
            </div>

            <div class="flex items-center gap-2 pt-6">
                <input id="urgent" name="urgent" type="checkbox" value="1"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @checked(old('urgent')) />
                <x-input-label for="urgent" :value="__('report.urgent_label')" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('urgent')" />
        </div>

        @php
            $oldAttachments = old('attachments', []);
            if (empty($oldAttachments)) {
                $oldAttachments = [[]];
            }
            $nextAttachmentIndex = count($oldAttachments);
        @endphp

        <div>
            <x-input-label :value="__('report.attachments_label')" />
            <p class="mt-2 text-sm text-gray-500">
                {{ __('report.attachments_help') }}
            </p>
            <div id="attachmentsList" class="mt-4 space-y-4" data-next-index="{{ $nextAttachmentIndex }}">
                @foreach ($oldAttachments as $index => $attachment)
                    <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm attachment-item" data-attachment-index="{{ $index }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="attachment-file-{{ $index }}">{{ __('report.attachments_file_label') }}</label>
                            <input id="attachment-file-{{ $index }}" name="attachments[{{ $index }}][file]" type="file"
                                accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-file-input" />
                            <x-input-error class="mt-2" :messages="$errors->get('attachments.' . $index . '.file')" />
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700" for="attachment-comment-{{ $index }}">{{ __('report.attachments_comment_label') }}</label>
                            <textarea id="attachment-comment-{{ $index }}" name="attachments[{{ $index }}][comment]" rows="2" maxlength="500"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-comment-input">{{ old('attachments.' . $index . '.comment') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('attachments.' . $index . '.comment')" />
                        </div>
                        <div class="mt-3 text-right">
                            <button type="button" class="inline-flex items-center text-sm font-semibold text-red-600 hover:text-red-500 remove-attachment-btn"
                                data-remove-index="{{ $index }}">
                                <i class="fas fa-times mr-1"></i> {{ __('report.attachments_remove') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">
                <button type="button" id="addAttachmentBtn"
                    class="inline-flex items-center rounded-md border border-indigo-500 px-3 py-1.5 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-plus mr-1"></i> {{ __('report.attachments_add') }}
                </button>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('attachments')" />
            <x-input-error class="mt-2" :messages="$errors->get('attachments.*.file')" />
            <x-input-error class="mt-2" :messages="$errors->get('attachments.*.comment')" />
            <div id="attachmentsPreview" class="mt-4 space-y-3" aria-live="polite"></div>
        </div>

        <div class="mt-4">
            <label class="inline-flex items-start space-x-2">
                <input type="checkbox"
                    name="attachment_may_contain_sensitive_content"
                    value="1"
                    class="form-checkbox h-4 w-4 text-indigo-600">
                <span class="text-sm text-gray-700">
                    {{ __('This attachment may contain nudity or graphic content.') }}
                </span>
            </label>
            <x-input-error class="mt-2" :messages="$errors->get('attachment_may_contain_sensitive_content')" />
        </div>

        <template id="attachment-template">
            <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm attachment-item" data-attachment-index="__INDEX__">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="attachment-file-__INDEX__">File</label>
                    <input id="attachment-file-__INDEX__" name="attachments[__INDEX__][file]" type="file"
                        accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-file-input" />
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700" for="attachment-comment-__INDEX__">Comment (optional)</label>
                    <textarea id="attachment-comment-__INDEX__" name="attachments[__INDEX__][comment]" rows="2" maxlength="500"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-comment-input"></textarea>
                </div>
                <div class="mt-3 text-right">
                    <button type="button" class="inline-flex items-center text-sm font-semibold text-red-600 hover:text-red-500 remove-attachment-btn"
                        data-remove-index="__INDEX__">
                        <i class="fas fa-times mr-1"></i> Remove attachment
                    </button>
                </div>
            </div>
        </template>

        <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
            <h3 class="text-sm font-semibold text-gray-800">Optional Voice Message (3 minutes per recording)</h3>
            <p class="mt-1 text-sm text-gray-600">
                You can speak instead of typing if that feels easier for you.
            </p>
            <p class="mt-1 text-sm text-gray-600">
                Recordings are limited to 3 minutes each, but you can upload as many separate recordings as you need.
            </p>
            <p class="mt-1 text-sm text-gray-600">
                Your voice can be automatically disguised to protect your identity.
            </p>
            <div class="mt-4 voice-recorder-control">
                <button type="button" id="recorder" class="recorder-button" aria-pressed="false">
                    <span class="sr-only">{{ __('report.voice_toggle_label') }}</span>
                    <svg class="recorder-icon recorder-icon--record" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                    <svg class="recorder-icon recorder-icon--arrow" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            d="M12 3a1 1 0 0 1 1 1v9.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 1 1 1.414-1.414L11 13.586V4a1 1 0 0 1 1-1Z">
                        </path>
                        <path d="M5 19a1 1 0 0 1 1-1h12a1 1 0 0 1 0 2H6a1 1 0 0 1-1-1Z"></path>
                    </svg>
                </button>
                <button type="button" id="recordClearBtn"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    disabled>
                    {{ __('report.voice_remove_button') }}
                </button>
            </div>
            <p id="recordingStatus" class="mt-3 text-sm text-gray-500"></p>
            <audio id="voicePreview" controls class="mt-4 hidden w-full rounded-lg bg-white"></audio>
            <div class="mt-2 flex gap-2">
                <button type="button" id="voicePlayBtn"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    disabled>
                    {{ __('report.voice_play') }}
                </button>
            </div>
            <input type="file" id="voiceRecordingInput" name="voice_recording" class="hidden" accept="audio/webm">
            <x-input-error class="mt-3" :messages="$errors->get('voice_recording')" />
            <div class="mt-4">
                <x-input-label for="voice_comment" :value="__('report.voice_comment_label')" />
                <textarea id="voice_comment" name="voice_comment" rows="2" maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('voice_comment') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('voice_comment')" />
            </div>
        </div>

        <div class="rounded-md border border-gray-200 bg-gray-50 p-4 text-xs text-gray-600">
            <p>
                {{ __('report.disclaimer') }}
            </p>
        </div>

        <div class="flex justify-end">
            <x-primary-button>{{ __('report.submit_button') }}</x-primary-button>
        </div>
    </form>

    <footer class="mt-8 text-center text-xs text-gray-600 space-y-2 border-t border-gray-200 pt-4">
        <p class="flex flex-col sm:flex-row items-center justify-center gap-2">
            <span>{{ __('New organization?') }}</span>
            <a href="{{ route('signup.show') }}" class="text-indigo-600 underline font-semibold">{{ __('Get Started') }}</a>
        </p>
        <p>
            <a href="{{ route('report.create') }}" class="text-indigo-600 underline">{{ __('Submit A Report') }}</a>
            &middot;
            <a href="{{ route('support') }}" class="text-indigo-600 underline">Support</a>
            &middot;
            <a href="{{ route('privacy') }}" class="text-indigo-600 underline">Privacy</a>
            &middot;
            <a href="{{ route('terms') }}" class="text-indigo-600 underline">Terms</a>
        </p>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoriesMap = @json($categories);
            const hrCategories = @json($hrCategories ?? []);
            const typeCategoryMap = @json($typeCategoryMap ?? []);
            const typeSelect = document.getElementById('type');
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const categoryPlaceholder = @json(__('report.category_placeholder'));
            const subcategoryPlaceholder = @json(__('report.subcategory_placeholder'));
            const archivedLabel = @json(__('report.archived_label'));
            let initialCategory = @json(old('category'));
            let initialSubcategory = @json(old('subcategory'));

            if (!categoriesMap || Object.keys(categoriesMap).length === 0) {
                if (categorySelect) {
                    categorySelect.innerHTML = '';
                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = categoryPlaceholder;
                    categorySelect.appendChild(placeholder);
                    categorySelect.disabled = true;
                }

                if (subcategorySelect) {
                    subcategorySelect.innerHTML = '';
                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = subcategoryPlaceholder;
                    subcategorySelect.appendChild(placeholder);
                    subcategorySelect.disabled = true;
                }

                return;
            }

            function allowedCategoriesForType(typeValue) {
                const allowed = typeCategoryMap[typeValue];
                if (!Array.isArray(allowed) || allowed.length === 0) {
                    return Object.keys(categoriesMap);
                }

                const sourceMap = typeValue === 'hr' ? { ...hrCategories, ...categoriesMap } : categoriesMap;

                const filtered = allowed.filter(category => sourceMap[category]);

                if (filtered.length === 0 && typeValue === 'hr' && Object.keys(hrCategories).length > 0) {
                    return Object.keys(hrCategories);
                }

                return filtered;
            }

            function populateSubcategories(selectedCategory, targetSubcategory = '') {
                if (!subcategorySelect) {
                    return;
                }

                subcategorySelect.innerHTML = '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = subcategoryPlaceholder;
                subcategorySelect.appendChild(placeholderOption);

                const sourceMap = typeSelect?.value === 'hr'
                    ? { ...hrCategories, ...categoriesMap }
                    : categoriesMap;
                const options = sourceMap[selectedCategory];
                if (!selectedCategory || !Array.isArray(options)) {
                    if (targetSubcategory) {
                        const archivedOption = document.createElement('option');
                        archivedOption.value = targetSubcategory;
                        archivedOption.textContent = `${targetSubcategory} (${archivedLabel})`;
                        archivedOption.selected = true;
                        subcategorySelect.appendChild(archivedOption);
                    }
                    subcategorySelect.value = '';
                    subcategorySelect.disabled = true;
                    return;
                }

                options.forEach(function (subcategory) {
                    const option = document.createElement('option');
                    option.value = subcategory;
                    option.textContent = subcategory;
                    subcategorySelect.appendChild(option);
                });

                subcategorySelect.disabled = false;

                if (targetSubcategory && options.includes(targetSubcategory)) {
                    subcategorySelect.value = targetSubcategory;
                } else {
                    subcategorySelect.value = '';
                }
            }

            function renderCategoryOptions(preferredCategory = '') {
                if (!categorySelect) {
                    return;
                }

                const allowed = allowedCategoriesForType(typeSelect ? typeSelect.value : '');
                categorySelect.innerHTML = '';

                const placeholderOption = document.createElement('option');
                placeholderOption.value = '';
                placeholderOption.textContent = categoryPlaceholder;
                categorySelect.appendChild(placeholderOption);

                let selectionApplied = false;
                allowed.forEach(function (categoryName) {
                    const option = document.createElement('option');
                    option.value = categoryName;
                    option.textContent = categoryName;
                    if (!selectionApplied && categoryName === preferredCategory) {
                        option.selected = true;
                        selectionApplied = true;
                    }
                    categorySelect.appendChild(option);
                });

                if (!selectionApplied && preferredCategory && !categoriesMap[preferredCategory]) {
                    const archivedOption = document.createElement('option');
                    archivedOption.value = preferredCategory;
                    archivedOption.textContent = `${preferredCategory} (${archivedLabel})`;
                    archivedOption.selected = true;
                    categorySelect.appendChild(archivedOption);
                    selectionApplied = true;
                }

                if (!selectionApplied) {
                    categorySelect.value = '';
                }

                categorySelect.disabled = allowed.length === 0;
                populateSubcategories(categorySelect.value, initialSubcategory);
                initialSubcategory = '';
            }

            categorySelect?.addEventListener('change', function (event) {
                populateSubcategories(event.target.value);
            });

            typeSelect?.addEventListener('change', function () {
                renderCategoryOptions(categorySelect ? categorySelect.value : '');
            });

            renderCategoryOptions(initialCategory);
            initialCategory = '';
        });
    </script>

    @if (!isset($lockedOrg))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const orgTypeMap = @json($orgTypeMap ?? []);
                const defaultTypes = @json(array_keys($types ?? []));
                const orgSelect = document.getElementById('org_id');
                const typeSelect = document.getElementById('type');

                if (!orgSelect || !typeSelect) {
                    return;
                }

                const optionLookup = Array.from(typeSelect.options).reduce((acc, option) => {
                    acc[option.value] = option;
                    return acc;
                }, {});

                function refreshTypeOptions(orgId) {
                    const allowed = orgTypeMap[orgId] && orgTypeMap[orgId].length
                        ? orgTypeMap[orgId]
                        : defaultTypes;
                    const allowedSet = new Set(allowed);
                    const previousValue = typeSelect.value;

                    Object.entries(optionLookup).forEach(([value, option]) => {
                        if (!value) {
                            return;
                        }
                        option.hidden = !allowedSet.has(value);
                    });

                    if (!allowedSet.has(typeSelect.value)) {
                        typeSelect.value = allowed[0] || defaultTypes[0] || 'safety';
                    }

                    if (typeSelect.value !== previousValue) {
                        typeSelect.dispatchEvent(new Event('change'));
                    }
                }

                orgSelect.addEventListener('change', () => refreshTypeOptions(orgSelect.value || ''));
                refreshTypeOptions(orgSelect.value || '');
            });
        </script>
        @endif

        @if ($recipientsEnabled)
            <div class="rounded-md border border-indigo-200 bg-indigo-50 p-4">
                <h3 class="text-sm font-semibold text-indigo-900">{{ __('report.recipients_title') }}</h3>
                <p class="mt-1 text-xs text-indigo-700">
                    {{ __('report.recipients_help') }}
                </p>
                <div id="recipient-message" class="mt-3 text-sm text-indigo-900">
                    {{ __('report.recipients_placeholder') }}
                </div>
                <div id="recipient-list" class="mt-3 space-y-2"></div>
                <x-input-error class="mt-2" :messages="$errors->get('recipients')" />
            </div>
        @endif

    <style>
        .voice-recorder-control {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .voice-recorder-control .recorder-button {
            position: relative;
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            background: #1f2937;
            border: 1px solid rgba(17, 24, 39, 0.2);
            cursor: pointer;
            box-shadow: 0 1px 4px rgba(12, 12, 13, 0.2), 0 0 0 1px rgba(15, 23, 42, 0.15);
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .voice-recorder-control .recorder-button:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.4);
        }

        .voice-recorder-control .recorder-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .voice-recorder-control .recorder-icon {
            pointer-events: none;
            position: absolute;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .voice-recorder-control .recorder-icon--record {
            width: 58%;
            height: 58%;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .voice-recorder-control .recorder-icon--record circle {
            fill: #ef4444;
        }

        .voice-recorder-control .recorder-icon--arrow {
            width: 46%;
            height: 46%;
            left: 50%;
            top: 55%;
            transform: translate(-50%, -10px);
            opacity: 0;
            color: #22c55e;
        }

        .voice-recorder-control .recorder-button.recording .recorder-icon--record {
            animation: recorder-wiggle 0.8s ease-in-out infinite;
        }

        .voice-recorder-control .recorder-button.download .recorder-icon--record {
            transform: translate(-50%, -60%) scale(0.7);
            opacity: 0;
        }

        .voice-recorder-control .recorder-button.download .recorder-icon--arrow {
            opacity: 1;
            animation: recorder-arrow 0.6s ease-in-out infinite;
        }

        .voice-recorder-control .recorder-button.out .recorder-icon--record {
            animation: recorder-out 0.8s ease forwards;
        }

        @keyframes recorder-wiggle {
            0%, 100% {
                transform: translate(-50%, -50%) rotate(8deg);
            }

            50% {
                transform: translate(-50%, -50%) rotate(-8deg);
            }
        }

        @keyframes recorder-arrow {
            0% {
                transform: translate(-50%, -12px);
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: translate(-50%, 4px);
                opacity: 0;
            }
        }

        @keyframes recorder-out {
            0% {
                transform: translate(-50%, -60%) scale(0.7);
                opacity: 0;
            }

            40% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }

            100% {
                transform: translate(-50%, 12px);
                opacity: 0;
            }
        }
    </style>

    <script>
        (function () {
            const attachmentsList = document.getElementById('attachmentsList');
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');
            const attachmentTemplate = document.getElementById('attachment-template');
            const previewWrapper = document.getElementById('attachmentsPreview');
            const voiceInput = document.getElementById('voiceRecordingInput');
            const voiceCommentInput = document.getElementById('voice_comment');
            const trackedUrls = [];

            if (!previewWrapper) {
                return;
            }

            const clearPreviews = () => {
                while (trackedUrls.length) {
                    const url = trackedUrls.pop();
                    URL.revokeObjectURL(url);
                }
                previewWrapper.innerHTML = '';
            };

            const renderMessage = message => {
                const msg = document.createElement('p');
                msg.className = 'text-sm text-gray-500';
                msg.textContent = message;
                previewWrapper.appendChild(msg);
            };

            const formatBytes = bytes => {
                if (!bytes) {
                    return '0 KB';
                }
                const units = ['bytes', 'KB', 'MB', 'GB'];
                const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
                const value = bytes / Math.pow(1024, exponent);
                return `${value.toFixed(exponent === 0 ? 0 : 1)} ${units[exponent]}`;
            };

            const createPreviewForFile = (file, label) => {
                const container = document.createElement('div');
                container.className = 'rounded-md border border-gray-200 bg-white p-4 shadow-sm';

                const heading = document.createElement('div');
                heading.className = 'flex items-center justify-between gap-3';
                const title = document.createElement('p');
                title.className = 'break-all text-sm font-medium text-gray-800';
                title.textContent = label ?? file.name;
                heading.appendChild(title);

                const size = document.createElement('span');
                size.className = 'text-xs text-gray-500';
                size.textContent = formatBytes(file.size);
                heading.appendChild(size);
                container.appendChild(heading);

                const mime = (file.type || '').toLowerCase();
                const previewBlock = document.createElement('div');
                previewBlock.className = 'mt-3';

                if (mime.startsWith('audio/')) {
                    const url = URL.createObjectURL(file);
                    trackedUrls.push(url);
                    const audio = document.createElement('audio');
                    audio.controls = true;
                    audio.className = 'w-full';
                    audio.src = url;
                    previewBlock.appendChild(audio);
                } else if (mime.startsWith('video/')) {
                    const url = URL.createObjectURL(file);
                    trackedUrls.push(url);
                    const video = document.createElement('video');
                    video.controls = true;
                    video.className = 'w-full rounded';
                    video.src = url;
                    video.style.maxHeight = '240px';
                    previewBlock.appendChild(video);
                } else if (mime.startsWith('image/')) {
                    const url = URL.createObjectURL(file);
                    trackedUrls.push(url);
                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = label ?? file.name;
                    img.className = 'h-auto max-h-56 w-auto rounded border';
                    previewBlock.appendChild(img);
                } else {
                    const note = document.createElement('p');
                    note.className = 'text-xs text-gray-500';
                    note.textContent = mime ? `Selected file type: ${mime}` : 'Selected file ready to upload.';
                    previewBlock.appendChild(note);
                }

                container.appendChild(previewBlock);
                previewWrapper.appendChild(container);
            };

            const collectSelectedFiles = () => {
                const selections = [];

                if (attachmentsList) {
                    attachmentsList.querySelectorAll('.attachment-item').forEach(item => {
                        const fileInput = item.querySelector('.attachment-file-input');
                        if (fileInput?.files?.length) {
                            const file = fileInput.files[0];
                            const commentInput = item.querySelector('.attachment-comment-input');
                            const comment = commentInput?.value?.trim();
                            selections.push({ file, label: comment || file.name });
                        }
                    });
                }

                if (voiceInput?.files?.length) {
                    const file = voiceInput.files[0];
                    const comment = voiceCommentInput?.value?.trim();
                    selections.push({ file, label: comment || 'Voice recording' });
                }

                return selections;
            };

            const refreshAttachmentPreview = () => {
                clearPreviews();
                const selections = collectSelectedFiles();

                if (!selections.length) {
                    renderMessage('No attachments selected yet.');
                    return;
                }

                selections.forEach(({ file, label }) => createPreviewForFile(file, label));
            };

            const addAttachment = () => {
                if (!attachmentsList || !attachmentTemplate) {
                    return;
                }

                let nextIndex = Number(attachmentsList.dataset.nextIndex ?? attachmentsList.children.length ?? 0);
                if (!Number.isFinite(nextIndex)) {
                    nextIndex = attachmentsList.children.length;
                }

                attachmentsList.dataset.nextIndex = nextIndex + 1;

                const templateHtml = attachmentTemplate.innerHTML.replace(/__INDEX__/g, String(nextIndex));
                const container = document.createElement('div');
                container.innerHTML = templateHtml.trim();
                const fragment = document.createDocumentFragment();
                Array.from(container.children).forEach(child => fragment.appendChild(child));
                attachmentsList.appendChild(fragment);

                refreshAttachmentPreview();
            };

            if (addAttachmentBtn) {
                addAttachmentBtn.addEventListener('click', () => {
                    addAttachment();
                });
            }

            attachmentsList?.addEventListener('click', event => {
                const trigger = event.target.closest('.remove-attachment-btn');
                if (!trigger) {
                    return;
                }

                event.preventDefault();
                const item = trigger.closest('.attachment-item');
                if (item) {
                    const fileInput = item.querySelector('.attachment-file-input');
                    if (fileInput) {
                        const emptyTransfer = new DataTransfer();
                        fileInput.files = emptyTransfer.files;
                    }
                    item.remove();
                    refreshAttachmentPreview();
                }
            });

            attachmentsList?.addEventListener('change', event => {
                if (event.target.classList.contains('attachment-file-input')) {
                    refreshAttachmentPreview();
                }
            });

            attachmentsList?.addEventListener('input', event => {
                if (event.target.classList.contains('attachment-comment-input')) {
                    refreshAttachmentPreview();
                }
            });

            voiceInput?.addEventListener('change', refreshAttachmentPreview);
            voiceCommentInput?.addEventListener('input', refreshAttachmentPreview);

            window.refreshAttachmentPreview = refreshAttachmentPreview;
            refreshAttachmentPreview();
        })();

        (function () {
            const recorderBtn = document.getElementById('recorder');
            const clearBtn = document.getElementById('recordClearBtn');
            const statusEl = document.getElementById('recordingStatus');
            const previewEl = document.getElementById('voicePreview');
            const fileInput = document.getElementById('voiceRecordingInput');
            const voiceCommentInput = document.getElementById('voice_comment');
            const playBtn = document.getElementById('voicePlayBtn');

            if (!recorderBtn || !clearBtn || !statusEl || !previewEl || !fileInput || !playBtn) {
                return;
            }

            const recordingSupported = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia && window.MediaRecorder && window.DataTransfer);
            if (!recordingSupported) {
                recorderBtn.disabled = true;
                statusEl.textContent = 'Voice recordings are not supported in this browser. You can still upload an audio file instead.';
                clearBtn.disabled = true;
                return;
            }

            let mediaRecorder = null;
            let recordedChunks = [];
            let activeStream = null;
            let objectUrl = null;
            let autoStopTimer = null;

            const refreshAttachmentPreview = () => {
                if (typeof window.refreshAttachmentPreview === 'function') {
                    window.refreshAttachmentPreview();
                }
            };

            const updatePreviewFromFile = () => {
                const files = fileInput?.files ?? [];
                if (!files.length) {
                    previewEl.src = '';
                    previewEl.classList.add('hidden');
                    statusEl.textContent = '';
                    revokeObject();
                    playBtn.disabled = true;
                    return;
                }

                revokeObject();
                const file = files[0];
                objectUrl = URL.createObjectURL(file);
                previewEl.src = objectUrl;
                previewEl.load();
                previewEl.classList.remove('hidden');
                previewEl.currentTime = 0;
                statusEl.textContent = file.name || 'voice-recording.webm';
                playBtn.disabled = false;
            };

            const clearFileInput = () => {
                const empty = new DataTransfer();
                fileInput.files = empty.files;
            };

            const stopStream = () => {
                if (activeStream) {
                    activeStream.getTracks().forEach(track => track.stop());
                    activeStream = null;
                }
                if (autoStopTimer) {
                    clearTimeout(autoStopTimer);
                    autoStopTimer = null;
                }
            };

            const revokeObject = () => {
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
            };

            const setState = (state, message = '') => {
                recorderBtn.dataset.state = state;
                statusEl.textContent = message;
                recorderBtn.disabled = false;
                recorderBtn.classList.remove('recording', 'download', 'out');

                if (state === 'idle') {
                    recorderBtn.setAttribute('aria-pressed', 'false');
                    clearBtn.disabled = !fileInput.files.length;
                } else if (state === 'recording') {
                    recorderBtn.classList.add('recording');
                    recorderBtn.setAttribute('aria-pressed', 'true');
                    clearBtn.disabled = true;
                } else if (state === 'processing') {
                    recorderBtn.disabled = true;
                    clearBtn.disabled = true;
                } else if (state === 'captured') {
                    recorderBtn.setAttribute('aria-pressed', 'false');
                    clearBtn.disabled = false;
                    recorderBtn.classList.add('download');
                    setTimeout(() => {
                        recorderBtn.classList.remove('download');
                        recorderBtn.classList.add('out');
                        setTimeout(() => recorderBtn.classList.remove('out'), 800);
                    }, 900);
                }
            };

            const clearCurrentRecording = (message = '') => {
                recordedChunks = [];
                stopStream();
                revokeObject();
                if (previewEl.src?.startsWith('blob:')) {
                    URL.revokeObjectURL(previewEl.src);
                }
                mediaRecorder = null;
                previewEl.src = '';
                previewEl.classList.add('hidden');
                playBtn.disabled = true;
                clearFileInput();
                refreshAttachmentPreview();
                setState('idle', message);
            };

            fileInput.addEventListener('change', () => {
                updatePreviewFromFile();
                refreshAttachmentPreview();
            });

            recorderBtn.addEventListener('click', async () => {
                const currentState = recorderBtn.dataset.state || 'idle';

                if (currentState === 'processing') {
                    return;
                }

                if (currentState === 'recording') {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        setState('processing', 'Processing recording...');
                    }
                    return;
                }

                clearCurrentRecording('');
                setState('processing', 'Requesting microphone access...');

                try {
                    activeStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                } catch (error) {
                    setState('idle', 'Microphone permission was denied. Please allow access or upload an audio file.');
                    return;
                }

                recordedChunks = [];
                mediaRecorder = new MediaRecorder(activeStream, { mimeType: 'audio/webm' });

                mediaRecorder.ondataavailable = event => {
                    if (event.data && event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = () => {
                    stopStream();

                    const chunks = recordedChunks.slice();
                    recordedChunks = [];
                    mediaRecorder = null;

                    if (!chunks.length) {
                        setState('idle', 'No audio recorded. Try again.');
                        return;
                    }

                    const blob = new Blob(chunks, { type: 'audio/webm' });
                    const fileName = `voice-report-${Date.now()}.webm`;
                    const file = new File([blob], fileName, { type: blob.type, lastModified: Date.now() });

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    fileInput.files = transfer.files;

                    revokeObject();
                    objectUrl = URL.createObjectURL(blob);
                    previewEl.src = objectUrl;
                    previewEl.load();
                    previewEl.classList.remove('hidden');
                    previewEl.currentTime = 0;
                    playBtn.disabled = false;

                    refreshAttachmentPreview();
                    setState('captured', 'Recording captured. Use the player above to listen before submitting.');
                };

                mediaRecorder.onerror = () => {
                    recordedChunks = [];
                    setState('idle', 'Recording failed. Please try again.');
                };

                mediaRecorder.start();
                setState('recording', 'Recording... speak clearly into your microphone.');

                autoStopTimer = setTimeout(() => {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        setState('processing', 'Processing recording...');
                    }
                }, 3 * 60 * 1000);
            });

            clearBtn.addEventListener('click', () => {
                clearCurrentRecording('Recording removed.');
                if (voiceCommentInput) {
                    voiceCommentInput.value = '';
                }
            });

            playBtn.addEventListener('click', () => {
                if (previewEl.src) {
                    previewEl.currentTime = 0;
                    previewEl.play().catch(() => {});
                }
            });

            window.addEventListener('beforeunload', () => {
                stopStream();
                revokeObject();
            });

            setState('idle', '');
        })();
    </script>

    @if ($recipientsEnabled)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const recipientsByOrg = @json($recipientMap);
                const orgSelect = document.getElementById('org_id');
                const listEl = document.getElementById('recipient-list');
                const messageEl = document.getElementById('recipient-message');
                const previousSelections = new Set(@json(old('recipients', [])));

                function renderRecipients(orgId) {
                    if (!listEl || !messageEl) {
                        return;
                    }

                    const recipients = recipientsByOrg[orgId] || [];
                    listEl.innerHTML = '';

                    if (!recipients.length) {
                        messageEl.textContent = orgId
                            ? '{{ __('report.recipients_empty') }}'
                            : '{{ __('report.recipients_placeholder') }}';
                        return;
                    }

                    messageEl.textContent = '';

                    recipients.forEach(function (recipient) {
                        const checkboxId = `recipient-${recipient.id}`;
                        const wrapper = document.createElement('div');
                        wrapper.className = 'flex items-center gap-2 rounded-md border border-indigo-100 bg-white px-3 py-2';

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.name = 'recipients[]';
                        checkbox.value = recipient.id;
                        checkbox.id = checkboxId;
                        checkbox.className = 'h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500';
                        checkbox.checked = previousSelections.has(String(recipient.id)) || orgId !== '' && previousSelections.size === 0;

                        const label = document.createElement('label');
                        label.htmlFor = checkboxId;
                        label.className = 'flex-1 text-sm text-gray-800';
                        label.innerHTML = `<span class="font-medium">${recipient.value}</span>
                            <span class="ml-2 text-xs uppercase tracking-wide text-gray-500">${recipient.department ?? ''}</span>`;

                        wrapper.appendChild(checkbox);
                        wrapper.appendChild(label);
                        listEl.appendChild(wrapper);
                    });
                }

                orgSelect?.addEventListener('change', function (event) {
                    previousSelections.clear(); // reset selections on org change
                    renderRecipients(event.target.value);
                });

                renderRecipients(orgSelect?.value || '');
            });
        </script>
    @endif

    <footer class="mt-16 border-t border-gray-200 pt-6 text-center text-sm text-gray-500">
        <p>
            {{ __('report.footer_monitoring') }}
        </p>
        <p class="mt-2 text-gray-600">
            Need help? Email <a href="mailto:{{ $infoEmail }}" class="text-indigo-600 underline">{{ $infoEmail }}</a> or <a href="mailto:{{ $supportEmail }}" class="text-indigo-600 underline">{{ $supportEmail }}</a>.
        </p>
        <p class="mt-2">&copy; {{ now()->year }} {{ __('report.footer_brand') }}</p>
    </footer>
</x-guest-layout>

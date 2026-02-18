<x-admin-layout>
    <x-slot name="header">
        {{ __('Edit Report') }}
    </x-slot>

    <div class="row">
        <div class="col-lg-8">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                    <h3 class="card-title mb-0">
                        {{ __('Report :id', ['id' => $report->id]) }}
                    </h3>
                    <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
                        <i class="fas fa-arrow-left mr-1"></i> {{ __('Back to details') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reports.update', $report) }}">
                        @csrf
                        @method('PUT')

                        @php
                            $categoryMap = $categories ?? [];
                            $selectedCategory = old('category', $report->category);
                            $initialSubcategories = $selectedCategory ? ($categoryMap[$selectedCategory] ?? []) : [];
                            $selectedSubcategory = old('subcategory', $report->subcategory);
                        @endphp

                        <div class="form-group">
                            <label for="category">{{ __('Category') }}</label>
                            <select id="category" name="category" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">{{ __('Select a category') }}</option>
                                @foreach ($categoryMap as $categoryName => $subcategoryList)
                                    <option value="{{ $categoryName }}" @selected($selectedCategory === $categoryName)>{{ $categoryName }}</option>
                                @endforeach
                                @if ($selectedCategory && ! array_key_exists($selectedCategory, $categoryMap))
                                    <option value="{{ $selectedCategory }}" selected>{{ $selectedCategory }} ({{ __('archived') }})</option>
                                @endif
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="type">{{ __('Type') }}</label>
                <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                    @foreach (['safety' => __('Safety & Threat'), 'commendation' => __('Commendation'), 'hr' => __('HR Anonymous')] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $report->type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-md-6">
                <label for="severity">{{ __('Severity') }}</label>
                <select id="severity" name="severity" class="form-control @error('severity') is-invalid @enderror" required>
                    @foreach (['low' => __('Low'), 'moderate' => __('Moderate'), 'high' => __('High'), 'critical' => __('Critical')] as $value => $label)
                        <option value="{{ $value }}" @selected(old('severity', $report->severity) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('severity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

                        <div class="form-group">
                            <label for="subcategory">{{ __('Subcategory') }}</label>
                            <select id="subcategory" name="subcategory" class="form-control @error('subcategory') is-invalid @enderror"
                                {{ $selectedCategory ? '' : 'disabled' }} required>
                                <option value="">{{ __('Select a subcategory') }}</option>
                                @foreach ($initialSubcategories as $subcategory)
                                    <option value="{{ $subcategory }}" @selected($selectedSubcategory === $subcategory)>{{ $subcategory }}</option>
                                @endforeach
                                @if ($selectedSubcategory && ! in_array($selectedSubcategory, $initialSubcategories, true))
                                    <option value="{{ $selectedSubcategory }}" selected>{{ $selectedSubcategory }} ({{ __('archived') }})</option>
                                @endif
                            </select>
                            @error('subcategory')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea id="description" name="description" rows="6"
                                class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $report->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="violation_date">{{ __('Violation date (optional)') }}</label>
                                <input type="date" id="violation_date" name="violation_date"
                                    class="form-control @error('violation_date') is-invalid @enderror"
                                    value="{{ old('violation_date', optional($report->violation_date)->format('Y-m-d')) }}">
                                @error('violation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="contact_name">{{ __('Reporter name (optional)') }}</label>
                                <input type="text" id="contact_name" name="contact_name"
                                    class="form-control @error('contact_name') is-invalid @enderror"
                                    value="{{ old('contact_name', $report->contact_name) }}" maxlength="150">
                                @error('contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="contact_email">{{ __('Reporter email (optional)') }}</label>
                                <input type="email" id="contact_email" name="contact_email"
                                    class="form-control @error('contact_email') is-invalid @enderror"
                                    value="{{ old('contact_email', $report->contact_email) }}">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="contact_phone">{{ __('Reporter phone (optional)') }}</label>
                                <input type="text" id="contact_phone" name="contact_phone"
                                    class="form-control @error('contact_phone') is-invalid @enderror"
                                    value="{{ old('contact_phone', $report->contact_phone) }}" maxlength="30">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="status">{{ __('Status') }}</label>
                                <select id="status" name="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="open" @selected(old('status', $report->status) === 'open')>{{ __('Open') }}</option>
                                    <option value="in_review" @selected(old('status', $report->status) === 'in_review')>{{ __('In review') }}</option>
                                    <option value="closed" @selected(old('status', $report->status) === 'closed')>{{ __('Closed') }}</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" id="urgent" name="urgent" value="1" class="form-check-input"
                                @checked(old('urgent', $report->urgent))>
                            <label for="urgent" class="form-check-label">{{ __('Marked as urgent') }}</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('reports.show', $report) }}" class="btn btn-outline-secondary mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i> {{ __('Save changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoriesMap = @json($categoryMap);
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const placeholder = @json(__('Select a subcategory'));
            const initialCategory = @json(old('category', $report->category));
            const initialSubcategory = @json(old('subcategory', $report->subcategory));
            const archivedLabel = @json(__('archived'));

            function renderSubcategories(selectedCategory, chosen = '') {
                if (!subcategorySelect) {
                    return;
                }

                subcategorySelect.innerHTML = '';

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = placeholder;
                subcategorySelect.appendChild(defaultOption);

                const options = categoriesMap[selectedCategory];
                if (!selectedCategory || !Array.isArray(options)) {
                    if (selectedCategory && selectedCategory === initialCategory && initialSubcategory) {
                        const archivedOption = document.createElement('option');
                        archivedOption.value = initialSubcategory;
                        archivedOption.textContent = `${initialSubcategory} (${archivedLabel})`;
                        archivedOption.selected = true;
                        subcategorySelect.appendChild(archivedOption);
                    }
                    subcategorySelect.value = '';
                    subcategorySelect.disabled = true;
                    return;
                }

                options.forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;
                    subcategorySelect.appendChild(option);
                });

                subcategorySelect.disabled = false;

                if (chosen && options.includes(chosen)) {
                    subcategorySelect.value = chosen;
                } else if (chosen) {
                    const archivedOption = document.createElement('option');
                    archivedOption.value = chosen;
                    archivedOption.textContent = `${chosen} (${archivedLabel})`;
                    archivedOption.selected = true;
                    subcategorySelect.appendChild(archivedOption);
                } else {
                    subcategorySelect.value = '';
                }
            }

            renderSubcategories(categorySelect?.value, @json(old('subcategory', $report->subcategory)));

            categorySelect?.addEventListener('change', function (event) {
                renderSubcategories(event.target.value);
            });
        });
    </script>
@endpush

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

                        <div class="form-group">
                            <label for="category">{{ __('Category') }}</label>
                            <input type="text" id="category" name="category" class="form-control @error('category') is-invalid @enderror"
                                value="{{ old('category', $report->category) }}" maxlength="100" required>
                            @error('category')
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


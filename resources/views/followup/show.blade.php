<x-guest-layout>
    <div class="mx-auto w-full max-w-4xl space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ __('Case reference') }}</p>
                    <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ __('Case Follow-Up Portal') }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Track status changes, review updates, and respond securely to your assigned reviewer.') }}
                    </p>
                </div>
                <div class="flex flex-col items-end gap-3 text-right">
                    <x-language-switcher />
                    <div>
                        <p class="text-xs font-medium text-gray-500">{{ __('Reference ID') }}</p>
                        <p class="font-mono text-lg text-gray-900">{{ $report->id }}</p>
                        <span class="mt-2 inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase text-indigo-700">
                            {{ str_replace('_', ' ', $report->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Category') }}</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">
                        {{ $report->category }}
                        <span class="block text-xs font-normal text-gray-500">
                            {{ $report->subcategory ?? __('Not provided') }}
                        </span>
                    </dd>
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Type') }}</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">
                        {{ $report->type_label }}
                        <span class="block text-xs font-normal text-gray-500">
                            {{ __('Severity') }}: {{ $report->severity_label }}
                        </span>
                    </dd>
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Submitted') }}</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">
                        {{ $report->created_at->format('M d, Y H:i') }}
                        <span class="block text-xs font-normal text-gray-500">
                            {{ __('Violation date') }}: {{ $report->violation_date?->format('M d, Y') ?? __('Not provided') }}
                        </span>
                    </dd>
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Urgent flag') }}</dt>
                    <dd class="mt-2 text-sm font-medium text-gray-900">
                        @if ($report->urgent)
                            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                {{ __('Marked urgent') }}
                            </span>
                        @else
                            <span class="text-xs font-normal text-gray-500">{{ __('Not marked urgent') }}</span>
                        @endif
                    </dd>
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4 sm:col-span-2 lg:col-span-3">
                    <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Description') }}</dt>
                    <dd class="mt-2 whitespace-pre-line text-sm text-gray-800">
                        {{ $report->description }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Case Updates') }}</h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Milestones from your submission are listed below. The latest status reflects the review team’s current progress.') }}
            </p>
            <ul class="mt-4 space-y-3 text-sm text-gray-700">
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-2 w-2 rounded-full bg-indigo-500"></span>
                    <div>
                        <p class="font-medium text-gray-900">{{ __('Report submitted') }}</p>
                        <p class="text-gray-600">{{ $report->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </li>
                @if ($report->first_response_at)
                    <li class="flex items-start gap-2">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        <div>
                            <p class="font-medium text-gray-900">{{ __('First reviewer response recorded') }}</p>
                            <p class="text-gray-600">{{ $report->first_response_at->format('M d, Y H:i') }}</p>
                        </div>
                    </li>
                @endif
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-2 w-2 rounded-full bg-amber-500"></span>
                    <div>
                        <p class="font-medium text-gray-900">{{ __('Current status') }}</p>
                        <p class="text-gray-600">
                            {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                            <span class="text-xs text-gray-500">
                                {{ __('Updated') }} {{ optional($report->updated_at)->format('M d, Y H:i') }}
                            </span>
                        </p>
                    </div>
                </li>
            </ul>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Evidence & Attachments') }}</h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('You can review previously uploaded evidence or save a copy for your records.') }}
            </p>
            <div class="mt-4 space-y-4">
                @forelse ($report->files as $file)
                    @php
                        $mime = $file->mime ?? '';
                        $previewUrl = route('followup.attachments.preview', [$report->chat_token, $file]);
                        $downloadUrl = route('followup.attachments.download', [$report->chat_token, $file]);
                    @endphp
                    <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $file->original_name }}</p>
                                <p class="text-xs text-gray-500">{{ $mime ?: __('File') }}</p>
                                @if ($file->comment)
                                    <p class="mt-1 text-xs text-gray-600 whitespace-pre-line">{{ $file->comment }}</p>
                                @endif
                                @if (str_starts_with($mime, 'audio/'))
                                    <audio controls preload="metadata" class="mt-3 w-full">
                                        <source src="{{ $previewUrl }}" type="{{ $mime }}">
                                        {{ __('Your browser does not support the audio element.') }}
                                    </audio>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ $previewUrl }}"
                                    class="inline-flex items-center rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Preview') }}
                                </a>
                                <a href="{{ $downloadUrl }}"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Download') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('No attachments were submitted with this report.') }}</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('Conversation History') }}</h2>
            <p class="mt-1 text-sm text-gray-600">
                {{ __('Send updates at any time. The review team will be notified when you post a new message.') }}
            </p>

            @if (session('ok'))
                <div class="mt-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                    {{ session('ok') }}
                </div>
            @endif

            <div class="mt-4 space-y-4">
                @forelse ($messages as $message)
                    <div
                        class="rounded-xl border border-gray-100 p-4 shadow-sm {{ $message->side === 'reviewer' ? 'ml-auto max-w-3xl border-indigo-100 bg-indigo-50' : 'bg-gray-50' }}">
                        <div class="flex items-center justify-between text-xs font-medium uppercase tracking-wide text-gray-500">
                            <span class="text-gray-700">
                                {{ $message->side === 'reviewer' ? __('Reviewer') : __('You') }}
                            </span>
                            <span class="text-gray-500">{{ $message->sent_at?->format('M d, Y H:i') }}</span>
                        </div>
                        <p class="mt-3 whitespace-pre-line text-sm text-gray-900">
                            {{ $message->message }}
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('No messages yet. Share an update below to start the conversation.') }}</p>
                @endforelse
            </div>

            <form method="POST" action="{{ route('followup.message', $report->chat_token) }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="message" class="sr-only">{{ __('Message') }}</label>
                    <textarea id="message" name="message" rows="4" required minlength="2" maxlength="5000"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('Write an update for the reviewing team...') }}">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <x-primary-button>{{ __('Send message') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

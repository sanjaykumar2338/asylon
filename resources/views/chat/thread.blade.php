<x-guest-layout>
    <div class="mx-auto w-full max-w-3xl space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-xl font-semibold text-gray-900">Report Conversation</h1>
            <p class="mt-1 text-sm text-gray-600">
                Reference ID: <span class="font-mono">{{ $report->id }}</span>
            </p>
            <p class="mt-1 text-xs text-gray-500">
                {{ __('Submitted on') }} {{ $report->created_at->format('M d, Y H:i') }}
            </p>
        </div>

        @if (session('ok'))
            <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('ok') }}
            </div>
        @endif

        <div class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Report Details') }}</h2>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Category') }}</dt>
                        <dd class="mt-1 text-sm text-gray-800">{{ $report->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Urgent flag') }}</dt>
                        <dd class="mt-1 text-sm text-gray-800">
                            @if ($report->urgent)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    {{ __('Marked urgent') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-500">{{ __('Not marked urgent') }}</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-gray-500">{{ __('Description') }}</dt>
                        <dd class="mt-1 whitespace-pre-line rounded-md border border-gray-100 bg-gray-50 p-4 text-sm text-gray-800">
                            {{ $report->description }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('Attachments') }}</h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('You can preview files inline or download them for safekeeping.') }}
                </p>
                <div class="mt-4 space-y-4">
                    @forelse ($report->files as $file)
                        @php
                            $mime = $file->mime ?? '';
                            $previewUrl = route('report.attachments.preview', [$report->chat_token, $file]);
                            $downloadUrl = route('report.attachments.download', [$report->chat_token, $file]);
                        @endphp
                        <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $file->original_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $mime ?: __('File') }}</p>
                                </div>
                                <a href="{{ $downloadUrl }}"
                                    class="inline-flex items-center rounded-md border border-indigo-500 px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ __('Download') }}
                                </a>
                            </div>
                            <div class="mt-3 space-y-3">
                                @if ($file->comment)
                                    <p class="text-sm text-gray-700 whitespace-pre-line">
                                        <span class="font-medium text-gray-900">{{ __('Comment') }}:</span> {{ $file->comment }}
                                    </p>
                                @endif
                                @if (str_starts_with($mime, 'audio/'))
                                    <audio controls preload="metadata" class="w-full">
                                        <source src="{{ $previewUrl }}" type="{{ $mime }}">
                                        {{ __('Your browser does not support the audio element.') }}
                                    </audio>
                                @elseif (str_starts_with($mime, 'video/'))
                                    <video controls preload="metadata" class="w-full rounded-md" style="max-height: 260px;">
                                        <source src="{{ $previewUrl }}" type="{{ $mime }}">
                                        {{ __('Your browser does not support the video element.') }}
                                    </video>
                                @elseif (str_starts_with($mime, 'image/'))
                                    <img src="{{ $previewUrl }}" alt="{{ $file->original_name }}"
                                        class="h-auto max-h-72 w-full rounded-md border object-contain">
                                @elseif (str_contains($mime, 'pdf'))
                                    <iframe src="{{ $previewUrl }}" class="h-72 w-full rounded-md border" title="{{ $file->original_name }}"></iframe>
                                @elseif (str_starts_with($mime, 'text/') || str_contains($mime, 'json') || str_contains($mime, 'xml') || str_contains($mime, 'csv'))
                                    <iframe src="{{ $previewUrl }}" class="h-72 w-full rounded-md border" title="{{ $file->original_name }}"></iframe>
                                @elseif (str_contains($mime, 'msword') || str_contains($mime, 'wordprocessingml') || str_contains($mime, 'ms-powerpoint') || str_contains($mime, 'presentation') || str_contains($mime, 'ms-excel') || str_contains($mime, 'spreadsheet'))
                                    <iframe src="{{ $previewUrl }}" class="h-72 w-full rounded-md border" title="{{ $file->original_name }}"></iframe>
                                    <p class="text-xs text-gray-500">
                                        {{ __('If you cannot see the document, use the download button to open it with your preferred application.') }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500">
                                        {{ __('Preview not available. Use the download button above to view this file.') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('No attachments were uploaded with this report.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            @forelse ($messages as $message)
                <div class="flex {{ $message->from === 'reviewer' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-md rounded-2xl px-4 py-3 text-sm leading-relaxed
                        {{ $message->from === 'reviewer' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                        <p class="whitespace-pre-line">{{ $message->body }}</p>
                        <p class="mt-2 text-xs text-white/80 {{ $message->from === 'reviewer' ? '' : 'text-gray-500' }}">
                            {{ ucfirst($message->from) }} • {{ $message->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No messages yet. You can send one below.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('chat.post', $report->chat_token) }}" class="space-y-4">
            @csrf
            <div>
                <label for="body" class="sr-only">Message</label>
                <textarea id="body" name="body" rows="4" required minlength="2" maxlength="5000"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Write a message to the review team...">{{ old('body') }}</textarea>
                @error('body')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end">
                <x-primary-button>Send message</x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

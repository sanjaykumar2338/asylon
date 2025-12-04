<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $pageTitle = $pageTitle
                ?? ($title ?? (isset($page) ? ($page->meta_title ?: $page->title) : config('app.name', 'Laravel')));
        @endphp
        <title>{{ $pageTitle }}</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
        @if(isset($page) && $page->meta_description)
            <meta name="description" content="{{ $page->meta_description }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @php
            $containerClass = $containerClass ?? 'w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg';
        @endphp
        @php
            $supportEmail = config('asylon.support_email', 'support@asylon.cc');
            $infoEmail = config('asylon.info_email', 'info@asylon.cc');
        @endphp
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="flex flex-col items-center w-full">
                <div class="flex items-center justify-between w-full max-w-6xl px-6">
                    <div class="flex items-center gap-3">
                        <a href="/">
                            <x-application-logo class="h-16 w-auto" />
                        </a>
                        <x-language-switcher class="lang-switch flex items-center gap-2 text-sm" />
                    </div>
                    @if(isset($headerMenu) && $headerMenu)
                        <nav class="hidden sm:flex items-center gap-4 text-sm font-medium text-gray-700">
                            @foreach($headerMenu->items as $item)
                                <a href="{{ $item->resolvedUrl() }}"
                                   target="{{ $item->target }}"
                                   class="hover:text-indigo-600">
                                    {{ $item->title }}
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>
            </div>

            <div class="{{ $containerClass }}">
                {{ $slot }}
                <footer class="mt-8 text-center text-xs text-gray-600 space-y-2 border-t border-gray-200 pt-4">
                    <p>
                        Contact us:
                        <a href="mailto:{{ $infoEmail }}" class="text-indigo-600 underline">{{ $infoEmail }}</a> |
                        <a href="mailto:{{ $supportEmail }}" class="text-indigo-600 underline">{{ $supportEmail }}</a>
                    </p>
                    @if(isset($footerMenu) && $footerMenu && $footerMenu->items->count())
                        <p class="flex flex-wrap justify-center gap-2">
                            @foreach($footerMenu->items as $item)
                                <a href="{{ $item->resolvedUrl() }}" target="{{ $item->target }}" class="text-indigo-600 underline">
                                    {{ $item->title }}
                                </a>
                                @if(! $loop->last)
                                    <span>&middot;</span>
                                @endif
                            @endforeach
                        </p>
                    @else
                        <p>
                            <a href="{{ route('support') }}" class="text-indigo-600 underline">Support</a>
                            &middot;
                            <a href="{{ route('privacy') }}" class="text-indigo-600 underline">Privacy</a>
                            &middot;
                            <a href="{{ route('terms') }}" class="text-indigo-600 underline">Terms</a>
                            &middot;
                            <a href="{{ route('privacy.anonymity') }}" class="text-indigo-600 underline">Privacy &amp; Anonymity</a>
                            &middot;
                            <a href="{{ route('security.overview') }}" class="text-indigo-600 underline">Security Overview</a>
                            &middot;
                            <a href="{{ url('/brand-info') }}" class="text-indigo-600 underline">Brand &amp; SMS Alerts Info</a>
                            &middot;
                            <a href="{{ route('report.create') }}" class="text-indigo-600 underline">Submit A Report</a>
                        </p>
                    @endif
                </footer>
            </div>
        </div>
        @php
            $flashOk = session('ok');
            $flashError = session('error');
        @endphp
        @if ($flashOk || $flashError)
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.Swal) {
                        @if ($flashOk)
                            Swal.fire({
                                icon: 'success',
                                title: @json($flashOk),
                                confirmButtonColor: '#2563eb'
                            });
                        @endif

                        @if ($flashError)
                            Swal.fire({
                                icon: 'error',
                                title: @json($flashError),
                                confirmButtonColor: '#dc2626'
                            });
                        @endif
                    }
                });
            </script>
        @endif
    </body>
</html>

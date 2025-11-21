<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="flex items-center justify-center gap-3">
                <a href="/">
                    <x-application-logo class="h-20 w-auto" />
                </a>
                <x-language-switcher class="lang-switch flex items-center gap-2 text-sm" />
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
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

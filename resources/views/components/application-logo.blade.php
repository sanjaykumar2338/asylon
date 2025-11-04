@props([
    'alt' => config('app.name', 'Application'),
])

@php
    $style = trim((string) $attributes->get('style'));
@endphp

<img
    src="{{ asset('assets/images/new-logo.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => 'h-10 w-auto'])->except('style') }}
    @if ($style !== '')
        style="{{ $style }}"
    @endif
>

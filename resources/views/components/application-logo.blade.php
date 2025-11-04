@props([
    'alt' => config('app.name', 'Application'),
])

@php
    $existingStyle = trim((string) $attributes->get('style'));
    $style = 'background-color: currentColor;' . ($existingStyle !== '' ? ' ' . $existingStyle : '');
@endphp

<img
    src="{{ asset('assets/images/new-logo.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => 'h-10 w-auto'])->except('style') }}
    style="{{ $style }}"
>

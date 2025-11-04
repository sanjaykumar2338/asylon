@props([
    'alt' => config('app.name', 'Application'),
])

<img src="{{ asset('assets/images/new-logo.png') }}" alt="{{ $alt }}" {{ $attributes->merge(['class' => 'h-10 w-auto']) }}>

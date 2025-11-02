@props(['messages'])

@php
    $messages = collect($messages)->flatten()->filter();
@endphp

@if ($messages->isNotEmpty())
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ($messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

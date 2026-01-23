@props(['messages'])

@php
    $messages = collect($messages)->flatten()->filter();
@endphp

@if ($messages->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'text-sm space-y-1']) }}>
        @foreach ($messages as $message)
            <p class="m-0 text-danger" style="color: #dc3545;">{{ $message }}</p>
        @endforeach
    </div>
@endif

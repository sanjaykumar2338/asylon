@push('meta')
    @if(isset($seo) && $seo)
        @if($seo->meta_description)
            <meta name="description" content="{{ $seo->meta_description }}">
        @elseif($page->meta_description)
            <meta name="description" content="{{ $page->meta_description }}">
        @endif
        @if($seo->meta_keywords)
            <meta name="keywords" content="{{ $seo->meta_keywords }}">
        @elseif($page->meta_keywords)
            <meta name="keywords" content="{{ $page->meta_keywords }}">
        @endif
    @else
        @if($page->meta_description)
            <meta name="description" content="{{ $page->meta_description }}">
        @endif
        @if($page->meta_keywords)
            <meta name="keywords" content="{{ $page->meta_keywords }}">
        @endif
    @endif
@endpush

<x-guest-layout :container-class="'w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg'">
    @php
        $h1 = $seo->h1_override ?? $page->title;
    @endphp
    <h1 class="text-3xl font-semibold text-gray-900 mb-4">{{ $h1 }}</h1>

    @if($page->excerpt)
        <p class="text-gray-600 mb-6">{{ $page->excerpt }}</p>
    @elseif($page->meta_description)
        <p class="text-gray-600 mb-6">{{ $page->meta_description }}</p>
    @endif

    <div class="prose prose-indigo max-w-none">
        {!! $page->content !!}
    </div>
</x-guest-layout>

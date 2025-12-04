<x-guest-layout :container-class="'w-full max-w-5xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg'">
    <h1 class="text-3xl font-semibold text-gray-900 mb-4">{{ $page->title }}</h1>

    @if($page->excerpt)
        <p class="text-gray-600 mb-6">{{ $page->excerpt }}</p>
    @elseif($page->meta_description)
        <p class="text-gray-600 mb-6">{{ $page->meta_description }}</p>
    @endif

    <div class="prose prose-indigo max-w-none">
        {!! $page->content !!}
    </div>
</x-guest-layout>

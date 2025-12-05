@php
    use Illuminate\Support\Str;
    $pageTitle = $seo->meta_title ?? 'Asylon Safety Insights';
    $pageDescription = $seo->meta_description ?? 'Insights on school, church, and workplace safety.';
    $h1 = $seo->h1_override ?? ($category->name ?? 'Asylon Safety Insights');
@endphp

<x-guest-layout :container-class="'w-full max-w-6xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg'" :pageTitle="$pageTitle">
    @if($pageDescription)
        @push('meta')
            <meta name="description" content="{{ $pageDescription }}">
            @if($seo?->meta_keywords)
                <meta name="keywords" content="{{ $seo->meta_keywords }}">
            @endif
        @endpush
    @endif

    <div class="mb-6 border-b border-gray-200 pb-4">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="text-3xl font-semibold text-gray-900">{{ $h1 }}</h1>
        @if($category ?? false)
            <p class="mt-2 text-sm text-gray-600">Category: {{ $category->name }}</p>
        @endif
    </div>

    @if($posts->isEmpty())
        <div class="border border-dashed border-gray-300 rounded-lg bg-gray-50 p-8 text-center text-gray-700">
            <p class="text-sm uppercase tracking-wide text-indigo-500 font-semibold mb-2">Blog</p>
            <h2 class="text-xl font-semibold text-gray-900 mb-2">Posts are coming soon</h2>
            <p class="text-sm text-gray-600">We’re preparing new safety insights. Check back shortly.</p>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                <article class="border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition">
                    @if($post->featuredImageUrl())
                        <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="w-full h-40 object-cover">
                    @endif
                    <div class="p-4 space-y-2">
                        @if($post->category)
                            <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded">{{ $post->category->name }}</span>
                        @endif
                        <h2 class="text-lg font-semibold text-gray-900">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-indigo-600">{{ $post->title }}</a>
                        </h2>
                        <p class="text-sm text-gray-700">{{ Str::limit($post->excerpt, 140) }}</p>
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-indigo-600 text-sm font-semibold">Read more →</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <div class="mt-6">
        {{ $posts->links() }}
    </div>
</x-guest-layout>

@php
    use Illuminate\Support\Str;
    $pageTitle = $post->meta_title ?: $post->title;
    $pageDescription = $post->meta_description ?: $post->excerpt;
    $canonical = url()->to(route('blog.show', $post->slug, false));
@endphp

<x-guest-layout :container-class="'w-full max-w-4xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg'" :pageTitle="$pageTitle">
    @push('meta')
        @if($pageDescription)
            <meta name="description" content="{{ $pageDescription }}">
        @endif
        @if($post->meta_keywords)
            <meta name="keywords" content="{{ $post->meta_keywords }}">
        @endif
        <link rel="canonical" href="{{ $canonical }}">
    @endpush

    <article class="space-y-4">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-500">Asylon</p>
        <h1 class="text-3xl font-semibold text-gray-900">{{ $post->title }}</h1>
        <div class="text-sm text-gray-600">
            @if($post->category)
                <span class="mr-2">Category: {{ $post->category->name }}</span>
            @endif
            @if($post->published_at)
                <span>Published: {{ $post->published_at->format('M d, Y') }}</span>
            @endif
        </div>

        @if($post->featuredImageUrl())
            <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="w-full h-auto rounded">
        @endif

        <div class="prose prose-indigo max-w-none">
            {!! $post->content !!}
        </div>

        @if($related->isNotEmpty())
            <div class="pt-6 border-t border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Related Posts</h3>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($related as $rel)
                        <div class="p-3 border rounded">
                            <a href="{{ route('blog.show', $rel->slug) }}" class="font-semibold text-indigo-700">{{ $rel->title }}</a>
                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($rel->excerpt, 100) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="pt-4">
            <a href="{{ route('blog.index') }}" class="text-indigo-600 underline text-sm">← Back to Blog</a>
        </div>
    </article>
</x-guest-layout>

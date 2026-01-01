@extends('layouts.website')

@php
    use Illuminate\Support\Str;
    $pageTitle = $post->meta_title ?: $post->title;
    $pageDescription = $post->meta_description ?: $post->excerpt;
    $canonical = url()->to(route('blog.show', $post->slug, false));
@endphp

@section('title', $pageTitle)

@push('meta')
    @if($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    @if($post->meta_keywords)
        <meta name="keywords" content="{{ $post->meta_keywords }}">
    @endif
    <link rel="canonical" href="{{ $canonical }}">
@endpush

@section('page-content')
    @php($blogImageFallback = asset('asylonhtml/asylon/images/mobile-pic.jpg'))

    <section class="pt-5 pb-5 bg-light border-bottom mt-5 mt-lg-5">
        <div class="site-container">
            <p class="text-uppercase text-primary small mb-2 fw-semibold">Asylon Blog</p>
            <h1 class="mb-2 fw-bold">{{ $post->title }}</h1>
            <div class="text-muted">
                @if($post->category)
                    <span class="me-3">Category: {{ $post->category->name }}</span>
                @endif
                @if($post->published_at)
                    <span>Published: {{ $post->published_at->format('M d, Y') }}</span>
                @endif
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="site-container">
            <article class="col-lg-10 mx-auto p-0">
                <?php
                    $imageUrl = $post->featuredImageUrl();
                    $imageSrc = $imageUrl ?: $blogImageFallback;
                    $imageAlt = $post->featured_image_alt ?? $post->title;
                ?>
                <img src="{{ $imageSrc }}"
                     onerror="this.src='{{ $blogImageFallback }}';"
                     alt="{{ $imageAlt }}"
                     class="img-fluid rounded mb-4 w-100"
                     style="max-height: 480px; object-fit: cover;"
                     loading="lazy">

                <div class="mb-4 blog-body">
                    {!! $post->content !!}
                </div>

                <?php
                if($related->isNotEmpty()){ ?>
                    <div class="pt-4 border-top">
                        <h3 class="h5 fw-bold mb-3">Related Posts</h3>
                        <div class="row">
                            <?php
                            foreach($related as $rel){
                                ?>
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 border rounded h-100">
                                        <a href="{{ route('blog.show', $rel->slug) }}" class="fw-bold text-dark text-decoration-none">{{ $rel->title }}</a>
                                        <p class="text-muted mb-0 small">{{ Str::limit($rel->excerpt, 120) }}</p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="pt-3">
                    <a href="{{ route('blog.index') }}" class="text-primary text-decoration-none">&larr; Back to Blog</a>
                </div>
            </article>
        </div>
    </section>
@endsection

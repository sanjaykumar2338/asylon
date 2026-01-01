@extends('layouts.website')

@php
    $seoTitle = optional($seo)->meta_title;
    $seoDescription = optional($seo)->meta_description;
    $seoKeywords = optional($seo)->meta_keywords;
    $seoH1 = optional($seo)->h1_override;

    $pageTitle = $seoTitle
        ?? optional($page)->meta_title
        ?? optional($page)->title
        ?? 'Asylon Safety Insights';

    $pageDescription = $seoDescription
        ?? optional($page)->meta_description
        ?? 'Insights on school, church, and workplace safety.';

    $h1 = $seoH1
        ?? ($category->name ?? null)
        ?? optional($page)->title
        ?? 'Asylon Safety Insights';

    $pageKeywords = $seoKeywords
        ?? optional($page)->meta_keywords;
@endphp

@section('title', $pageTitle)

@push('meta')
    @if($pageDescription)
        <meta name="description" content="{{ $pageDescription }}">
    @endif
    @if(!empty($pageKeywords))
        <meta name="keywords" content="{{ $pageKeywords }}">
    @endif
@endpush

@section('page-content')
    @php($blogImageFallback = asset('asylonhtml/asylon/images/mobile-pic.jpg'))

    <section class="pt-4 pb-4 bg-light border-bottom mt-4 mt-lg-5">
        <div class="site-container">
            <p class="text-uppercase text-primary small mb-2">Asylon</p>
            <h1 class="mb-2">{{ $h1 }}</h1>
            @if($category ?? false)
                <p class="text-muted mb-0">Category: {{ $category->name }}</p>
            @endif
        </div>
    </section>

    <section class="py-5">
        <div class="site-container">
            <div class="row g-4">

                <?php
                    foreach($posts as $post) {
                        $imageUrl = $post->featuredImageUrl();
                        $imageSrc = $imageUrl ?: $blogImageFallback;
                        $imageAlt = $post->featured_image_alt ?? $post->title;
                ?>
                    <div class="col-md-4">
                        <article class="card h-100 shadow-sm border-0">
                            <img src="{{ $imageSrc }}"
                                 onerror="this.src='{{ $blogImageFallback }}';"
                                 alt="{{ $imageAlt }}"
                                 class="card-img-top"
                                 style="height: 220px; object-fit: cover;"
                                 loading="lazy">
                            <div class="card-body d-flex flex-column">
                                @if($post->category)
                                    <span class="badge bg-light text-primary border mb-2 fw-semibold">{{ $post->category->name }}</span>
                                @endif
                                <h2 class="h5 fw-bold mb-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-dark text-decoration-none">{{ $post->title }}</a>
                                </h2>
                                <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($post->excerpt, 140) }}</p>
                                <div class="mt-auto">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-primary fw-semibold text-decoration-none">Read more</a>
                                </div>
                            </div>
                        </article>
                    </div>
                    <?php } ?>
            </div>

            <div class="mt-4">
                {{ $posts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </section>
@endsection

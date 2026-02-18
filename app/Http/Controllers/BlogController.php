<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Page;
use App\Models\SeoPage;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::where('status', 'published')
            ->with('category')
            ->orderByDesc('published_at')
            ->paginate(9);

        $seo = SeoPage::where('slug', 'blog')->first();
        $page = Page::where('slug', 'blog')
            ->where('published', true)
            ->first();

        return view('blog.index', compact('posts', 'seo', 'page'));
    }

    public function show(string $slug): View
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->with('category')
            ->firstOrFail();

        $related = BlogPost::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }

    public function category(string $slug): View
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        $posts = BlogPost::where('status', 'published')
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(9);
        $seo = SeoPage::where('slug', 'blog')->first();
        $page = Page::where('slug', 'blog')
            ->where('published', true)
            ->first();

        return view('blog.index', compact('posts', 'category', 'seo', 'page'));
    }
}

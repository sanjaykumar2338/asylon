<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query()->with('category')->latest();

        if ($search = $request->get('q')) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        $posts = $query->paginate(15)->withQueryString();
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.posts.index', compact('posts', 'categories'));
    }

    public function create(): View
    {
        $categories = BlogCategory::orderBy('name')->get();
        $post = new BlogPost(['status' => 'draft']);

        return view('admin.blog.posts.create', compact('post', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePost($request);

        $post = BlogPost::create($data);

        if ($post->status === 'published' && ! $post->published_at) {
            $post->update(['published_at' => now()]);
        }

        return redirect()->route('admin.blog-posts.edit', $post)->with('ok', 'Post created.');
    }

    public function edit(BlogPost $blog_post): View
    {
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.posts.edit', ['post' => $blog_post, 'categories' => $categories]);
    }

    public function update(Request $request, BlogPost $blog_post): RedirectResponse
    {
        $data = $this->validatePost($request, $blog_post);

        $blog_post->update($data);

        if ($blog_post->status === 'published' && ! $blog_post->published_at) {
            $blog_post->update(['published_at' => now()]);
        }

        return back()->with('ok', 'Post updated.');
    }

    public function destroy(BlogPost $blog_post): RedirectResponse
    {
        $blog_post->delete();

        return redirect()->route('admin.blog-posts.index')->with('ok', 'Post deleted.');
    }

    protected function validatePost(Request $request, ?BlogPost $post = null): array
    {
        $postId = $post?->id;

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('blog_posts', 'slug')->ignore($postId)],
            'category_id' => ['nullable', 'exists:blog_categories,id'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'featured_image_upload' => ['nullable', 'image', 'max:5120'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'author_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['title']);
        }

        $this->handleFeaturedImage($request, $data);

        return $data;
    }

    protected function handleFeaturedImage(Request $request, array &$data): void
    {
        if ($request->hasFile('featured_image_upload')) {
            $path = $request->file('featured_image_upload')->store('blog', 'public');
            $data['featured_image'] = $path;
        }
    }
}

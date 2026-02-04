<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

    public function create(Request $request): View
    {
        $formLocale = $this->resolveLocale($request);
        $categories = BlogCategory::orderBy('name')->get();
        $post = new BlogPost(['status' => 'draft']);

        return view('admin.blog.posts.create', compact('post', 'categories', 'formLocale'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePost($request);

        $locale = $data['locale'];
        unset($data['locale']);

        $post = BlogPost::create($data);

        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');

        $translatable = [
            'title',
            'excerpt',
            'content',
            'featured_image_alt',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'author_name',
        ];
        $translationPayload = Arr::only($data, $translatable);

        foreach ($translatable as $field) {
            if (array_key_exists($field, $translationPayload)) {
                $post->setTranslation($field, $locale, $translationPayload[$field]);
            }
        }

        if ($locale === $defaultLocale) {
            foreach ($languages as $language) {
                foreach ($translatable as $field) {
                    if (! filled($post->getTranslationValue($field, $language))) {
                        $post->setTranslation($field, $language, $translationPayload[$field] ?? null);
                    }
                }
            }
        } else {
            foreach ($translatable as $field) {
                if (! filled($post->getTranslationValue($field, $defaultLocale))) {
                    $post->setTranslation($field, $defaultLocale, $translationPayload[$field] ?? null);
                }
            }
        }

        $post->save();

        if ($post->status === 'published' && ! $post->published_at) {
            $post->update(['published_at' => now()]);
        }

        return redirect()
            ->route('admin.blog-posts.edit', ['blog_post' => $post, 'lang' => $locale])
            ->with('ok', 'Post created.');
    }

    public function edit(Request $request, BlogPost $blog_post): View
    {
        $formLocale = $this->resolveLocale($request);
        $categories = BlogCategory::orderBy('name')->get();

        return view('admin.blog.posts.edit', ['post' => $blog_post, 'categories' => $categories, 'formLocale' => $formLocale]);
    }

    public function update(Request $request, BlogPost $blog_post): RedirectResponse
    {
        $data = $this->validatePost($request, $blog_post);

        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $locale = $data['locale'];
        unset($data['locale']);

        $translatable = [
            'title',
            'excerpt',
            'content',
            'featured_image_alt',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'author_name',
        ];
        $translationPayload = Arr::only($data, $translatable);
        $baseData = Arr::except($data, $translatable);

        if ($locale === $defaultLocale) {
            $blog_post->fill($baseData + $translationPayload);
        } else {
            $blog_post->fill($baseData);
        }

        $blog_post->save();

        foreach ($translatable as $field) {
            if (array_key_exists($field, $translationPayload)) {
                $blog_post->setTranslation($field, $locale, $translationPayload[$field]);
            }
        }

        if ($locale === $defaultLocale) {
            foreach ($translatable as $field) {
                if (array_key_exists($field, $translationPayload)) {
                    $blog_post->setTranslation($field, $defaultLocale, $translationPayload[$field]);
                }
            }

            foreach ($languages as $language) {
                foreach ($translatable as $field) {
                    if (! filled($blog_post->getTranslationValue($field, $language))) {
                        $blog_post->setTranslation($field, $language, $translationPayload[$field] ?? null);
                    }
                }
            }
        }

        $blog_post->save();

        if ($blog_post->status === 'published' && ! $blog_post->published_at) {
            $blog_post->update(['published_at' => now()]);
        }

        return redirect()
            ->route('admin.blog-posts.edit', ['blog_post' => $blog_post, 'lang' => $locale])
            ->with('ok', 'Post updated.');
    }

    public function destroy(BlogPost $blog_post): RedirectResponse
    {
        $blog_post->delete();

        return redirect()->route('admin.blog-posts.index')->with('ok', 'Post deleted.');
    }

    protected function validatePost(Request $request, ?BlogPost $post = null): array
    {
        $postId = $post?->id;
        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $locale = $request->input('locale', $defaultLocale);
        $titleRule = $post ? ($locale === $defaultLocale ? 'required' : 'nullable') : 'required';

        $data = $request->validate([
            'locale' => ['required', 'string', Rule::in($languages)],
            'title' => [$titleRule, 'string', 'max:255'],
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
            if (! $post || $locale === $defaultLocale) {
                $baseTitle = $data['title'] ?? $post?->getRawOriginal('title') ?? '';
                $data['slug'] = Str::slug($baseTitle);
            } else {
                unset($data['slug']);
            }
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

    protected function resolveLocale(Request $request): string
    {
        $languages = array_keys(config('asylon.languages', []));
        $defaultLocale = config('app.fallback_locale', 'en');
        $locale = $request->query('lang', $defaultLocale);

        if (! in_array($locale, $languages, true)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);

        return $locale;
    }
}

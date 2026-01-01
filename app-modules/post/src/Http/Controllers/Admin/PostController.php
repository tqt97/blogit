<?php

namespace Modules\Post\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Post\Http\Requests\IndexPostRequest;
use Modules\Post\Http\Requests\StorePostRequest;
use Modules\Post\Http\Requests\UpdatePostRequest;
use Modules\Post\Models\Post;
use Modules\Post\Services\PostService;
use Modules\Shared\Contracts\Taxonomy\CategoryQuery;
use Modules\Shared\Contracts\Taxonomy\TagQuery;

class PostController
{
    public function index(
        IndexPostRequest $request,
        PostService $service,
        CategoryQuery $categories,
        TagQuery $tags,
    ): Response {
        $filter = $request->toFilter();
        $posts = $service->list($filter);

        return Inertia::render('admin/posts/index', [
            'posts' => $posts,
            'filters' => [
                'q' => $filter->q,
                'status' => $filter->status,
                'category_id' => $filter->categoryId,
                'tag_id' => $filter->tagId,
                'sort' => $filter->sort,
                'direction' => $filter->direction,
                'per_page' => $filter->perPage,
            ],
            'categories' => $categories->listForSelect(),
            'tags' => $tags->listForSelect(),
        ]);
    }

    public function create(CategoryQuery $categories, TagQuery $tags): Response
    {
        return Inertia::render('admin/posts/create', [
            'categories' => $categories->listForSelect(),
            'tags' => $tags->listForSelect(),
        ]);
    }

    public function store(StorePostRequest $request, PostService $service): RedirectResponse
    {
        $post = $service->create($request->validated(), $request->user()->id);

        return redirect()->route('posts.show', $post);
    }

    public function edit(Post $post, CategoryQuery $categories, TagQuery $tags): Response
    {
        $post->load('tags');  // chỉ load quan hệ nội bộ Post module

        return Inertia::render('admin/posts/edit', [
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'category_id' => $post->category_id,
                'tag_ids' => $post->tags->pluck('id')->map(fn ($id) => (int) $id)->all(),
                'status' => $post->status,
                'published_at' => optional($post->published_at)?->toISOString(),
            ],
            'categories' => $categories->listForSelect(),
            'tags' => $tags->listForSelect(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post, PostService $service): RedirectResponse
    {
        $service->update($post, $request->validated());

        return back()->with('success', 'Updated.');
    }
}

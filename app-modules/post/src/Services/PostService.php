<?php

namespace Modules\Post\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Post\DTOs\PostFilterDTO;
use Modules\Post\Models\Post;
use Modules\Post\Repositories\PostRepository;
use Modules\Shared\Contracts\Taxonomy\CategoryLookup;
use Modules\Shared\Contracts\Taxonomy\TagLookup;

class PostService
{
    public function __construct(
        private readonly PostRepository $posts,
        private readonly CategoryLookup $categoryLookup,
        private readonly TagLookup $tagLookup,
    ) {}

    public function list(PostFilterDTO $filter): LengthAwarePaginator
    {
        return $this->posts->paginate($filter);
    }

    public function create(array $payload, int $userId): Post
    {
        return DB::transaction(function () use ($payload, $userId) {
            $categoryId = $payload['category_id'] ?? null;
            if ($categoryId !== null) {
                $this->categoryLookup->assertExists((int) $categoryId);
            }

            $tagIds = $payload['tag_ids'] ?? [];
            $tagIds = $this->tagLookup->filterExistingIds(is_array($tagIds) ? $tagIds : []);

            $post = $this->posts->create([
                'user_id' => $userId,
                'category_id' => $categoryId ? (int) $categoryId : null,
                'title' => $payload['title'],
                'slug' => $payload['slug'],
                'excerpt' => $payload['excerpt'] ?? null,
                'content' => $payload['content'],
                'status' => $payload['status'] ?? 'draft',
                'published_at' => $payload['published_at'] ?? null,
            ]);

            $this->posts->syncTags($post, $tagIds);

            return $post;
        });
    }

    public function update(Post $post, array $payload): Post
    {
        return DB::transaction(function () use ($post, $payload) {
            $categoryId = $payload['category_id'] ?? null;
            if ($categoryId !== null) {
                $this->categoryLookup->assertExists((int) $categoryId);
            }

            $tagIds = $payload['tag_ids'] ?? [];
            $tagIds = $this->tagLookup->filterExistingIds(is_array($tagIds) ? $tagIds : []);

            // published_at rule “thực dụng”:
            // - nếu status = published mà published_at null => set now (tuỳ bạn)
            $status = $payload['status'] ?? $post->status;
            $publishedAt = $payload['published_at'] ?? $post->published_at;

            if ($status === 'published' && empty($publishedAt)) {
                $publishedAt = now();
            }

            $this->posts->update($post, [
                'category_id' => $categoryId ? (int) $categoryId : null,
                'title' => $payload['title'],
                'slug' => $payload['slug'],
                'excerpt' => $payload['excerpt'] ?? null,
                'content' => $payload['content'],
                'status' => $status,
                'published_at' => $publishedAt,
            ]);

            $this->posts->syncTags($post, $tagIds);

            return $post->refresh();
        });
    }
}

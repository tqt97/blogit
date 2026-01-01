<?php

namespace Modules\Post\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Post\DTOs\PostFilterDTO;
use Modules\Post\Models\Post;

class PostRepository
{
    public function paginate(PostFilterDTO $filter): LengthAwarePaginator
    {
        $q = Post::query()
            ->select([
                'posts.id',
                'posts.user_id',
                'posts.category_id',
                'posts.title',
                'posts.slug',
                'posts.status',
                'posts.published_at',
                'posts.created_at',
                'posts.likes_count',
                'posts.comments_count',
            ])
            ->with([
                'category:id,name',
                'author:id,name',
            ]);

        // Filter: status (tận dụng index status,published_at)
        if ($filter->status) {
            $q->where('posts.status', $filter->status);
        }

        // Filter: category (tận dụng index category_id)
        if ($filter->categoryId) {
            $q->where('posts.category_id', $filter->categoryId);
        }

        // Filter: tag (CHỈ join pivot khi cần)
        if ($filter->tagId) {
            $q->whereExists(function ($sub) use ($filter) {
                $sub
                    ->selectRaw('1')
                    ->from('post_tag')
                    ->whereColumn('post_tag.post_id', 'posts.id')
                    ->where('post_tag.tag_id', $filter->tagId);
            });
        }

        // Search q (đơn giản - LIKE). Nếu data lớn, cân nhắc FULLTEXT.
        if ($filter->q) {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $filter->q).'%';
            $q->where(function ($w) use ($like) {
                $w
                    ->where('posts.title', 'like', $like)
                    ->orWhere('posts.slug', 'like', $like);
            });
        }

        // Sort
        $sortCol = 'posts.'.$filter->sort;
        $q->orderBy($sortCol, $filter->direction);

        // Nếu sort = published_at mà có null (draft), bạn có thể thêm fallback:
        // $q->orderByRaw('posts.published_at is null')->orderBy('posts.published_at','desc');

        return $q->paginate($filter->perPage)->withQueryString();
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);

        return $post;
    }

    public function syncTags(Post $post, array $tagIds): void
    {
        $post->tags()->sync($tagIds);
    }
}

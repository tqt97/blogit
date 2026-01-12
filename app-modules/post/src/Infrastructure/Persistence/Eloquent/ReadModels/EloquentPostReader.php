<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Persistence\Eloquent\ReadModels;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Post\Application\DTOs\PostDTO;
use Modules\Post\Application\Ports\ReadModels\PostReadModel;
use Modules\Post\Domain\ValueObjects\Pagination;
use Modules\Post\Domain\ValueObjects\PostStatus;
use Modules\Post\Domain\ValueObjects\SearchTerm;
use Modules\Post\Domain\ValueObjects\Sorting;
use Modules\Post\Infrastructure\Persistence\Eloquent\Models\PostModel;
use Modules\Tag\Application\Ports\ReadModels\TagReadModel;

final class EloquentPostReader implements PostReadModel
{
    public function __construct(
        private readonly TagReadModel $tagReader,
    ) {}

    public function paginate(?SearchTerm $search, Pagination $pagination, Sorting $sorting): LengthAwarePaginator
    {
        $query = PostModel::query()
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'status',
                'views_count',
                'comments_count',
                'likes_count',
                'published_at',
                'created_at',
                'updated_at',
            ]);

        if ($search !== null) {
            $s = $search->value;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        $paginator = $query
            ->orderBy($sorting->field->value, $sorting->direction->value)
            ->paginate(perPage: $pagination->perPage, page: $pagination->page)
            ->withQueryString();

        // Hydrate tags for the chunk
        $postIds = $paginator->getCollection()->pluck('id')->toArray();
        $tagsByPost = $this->fetchTagsForPosts($postIds);

        return $paginator->through(fn (PostModel $model) => $this->mapToDTO($model, $tagsByPost[$model->id] ?? []));
    }

    public function find(int $id): ?PostDTO
    {
        $model = PostModel::query()
            ->select([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'status',
                'views_count',
                'comments_count',
                'likes_count',
                'published_at',
                'created_at',
                'updated_at',
            ])
            ->find($id);

        if (! $model) {
            return null;
        }

        $tagsByPost = $this->fetchTagsForPosts([$model->id]);

        return $this->mapToDTO($model, $tagsByPost[$model->id] ?? []);
    }

    private function fetchTagsForPosts(array $postIds): array
    {
        if (empty($postIds)) {
            return [];
        }

        // 1. Get pivots
        $pivots = DB::table('post_tag')
            ->whereIn('post_id', $postIds)
            ->select('post_id', 'tag_id')
            ->get();

        $tagIds = $pivots->pluck('tag_id')->unique()->toArray();

        // 2. Fetch Tag DTOs from Tag Module via Public Contract
        $tags = $this->tagReader->getByIds($tagIds);
        $tagsById = [];
        foreach ($tags as $tag) {
            $tagsById[$tag->id] = $tag;
        }

        // 3. Group by post_id
        $result = [];
        foreach ($pivots as $pivot) {
            if (isset($tagsById[$pivot->tag_id])) {
                $result[$pivot->post_id][] = $tagsById[$pivot->tag_id];
            }
        }

        return $result;
    }

    private function mapToDTO(PostModel $model, array $tags = []): PostDTO
    {
        return new PostDTO(
            id: (int) $model->id,
            title: (string) $model->title,
            slug: (string) $model->slug,
            excerpt: (string) $model->excerpt,
            content: (string) $model->content,
            status: $model->status instanceof PostStatus ? $model->status->value : (string) $model->status,
            publishedAt: $this->formatDate($model->published_at),
            viewCount: (int) $model->views_count,
            commentCount: (int) $model->comments_count,
            likeCount: (int) $model->likes_count,
            created_at: (string) $this->formatDate($model->created_at),
            updated_at: (string) $this->formatDate($model->updated_at),
            tags: $tags
        );
    }

    private function formatDate(mixed $date): ?string
    {
        if ($date instanceof Carbon) {
            return $date->toISOString();
        }

        if (is_string($date) && ! empty($date)) {
            try {
                return Carbon::parse($date)->toISOString();
            } catch (Exception) {
                return $date;
            }
        }

        return null;
    }
}

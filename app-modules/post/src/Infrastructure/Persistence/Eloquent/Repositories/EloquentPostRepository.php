<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Persistence\Eloquent\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Modules\Post\Domain\Entities\Post;
use Modules\Post\Domain\Exceptions\PostInUseException;
use Modules\Post\Domain\Exceptions\PostNotFoundException;
use Modules\Post\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Post\Domain\Repositories\PostRepository;
use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostIds;
use Modules\Post\Domain\ValueObjects\PostTagIds;
use Modules\Post\Infrastructure\Persistence\Eloquent\Mappers\PostMapper;
use Modules\Post\Infrastructure\Persistence\Eloquent\Models\PostModel;

final class EloquentPostRepository implements PostRepository
{
    public function __construct(private readonly PostMapper $mapper) {}

    public function save(Post $post): Post
    {
        try {
            $model = $post->id()
                ? PostModel::query()->findOrFail($post->id()->value())
                : new PostModel;

            $this->mapper->toPersistence($post, $model)->save();

            if ($post->id()) {
                return $post;
            }

            return $post->withId(new PostId((int) $model->id));
        } catch (QueryException $e) {
            if ($this->isUniqueConstraintViolation($e)) {
                throw new SlugAlreadyExistsException($e);
            }

            throw $e;
        } catch (ModelNotFoundException) {
            throw new PostNotFoundException;
        }
    }

    public function find(PostId $id): ?Post
    {
        $model = PostModel::query()->find($id->value());

        return $model ? $this->mapper->toEntity($model) : null;
    }

    public function delete(PostId $id): void
    {
        try {
            // Pivot rows usually delete on cascade if configured in DB migration (foreign keys)
            // but explicit delete is safer if cascades aren't trusted.
            // Migration had: $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            // So DB will handle pivot cleanup.

            $count = PostModel::query()->whereKey($id->value())->delete();

            if ($count === 0) {
                throw new PostNotFoundException;
            }
        } catch (QueryException $e) {
            if ($this->isForeignKeyConstraintViolation($e)) {
                throw new PostInUseException($e);
            }
            throw $e;
        }
    }

    public function deleteMany(PostIds $ids): void
    {
        $idValues = $ids->toScalars();

        try {
            PostModel::query()->whereIn('id', $idValues)->delete();
        } catch (QueryException $e) {
            if ($this->isForeignKeyConstraintViolation($e)) {
                throw new PostInUseException($e);
            }
            throw $e;
        }
    }

    public function incrementViews(PostId $id): void
    {
        try {
            $affected = PostModel::query()->where('id', $id->value())->increment('views_count');

            if ($affected === 0) {
                throw new PostNotFoundException;
            }
        } catch (ModelNotFoundException) {
            throw new PostNotFoundException;
        }
    }

    public function syncTags(PostId $id, PostTagIds $tags): bool
    {
        $postId = $id->value();
        $tagIds = $tags->ids();

        try {
            // 1. Get existing
            $existing = DB::table('post_tag')->where('post_id', $postId)->pluck('tag_id')->toArray();

            // 2. Determine to attach and detach
            $toAttach = array_diff($tagIds, $existing);
            $toDetach = array_diff($existing, $tagIds);

            if (empty($toAttach) && empty($toDetach)) {
                return false;
            }

            if (! empty($toDetach)) {
                DB::table('post_tag')
                    ->where('post_id', $postId)
                    ->whereIn('tag_id', $toDetach)
                    ->delete();
            }

            if (! empty($toAttach)) {
                $inserts = [];
                $now = now();
                foreach ($toAttach as $tagId) {
                    $inserts[] = [
                        'post_id' => $postId,
                        'tag_id' => $tagId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                DB::table('post_tag')->insert($inserts);
            }

            return true;
        } catch (QueryException $e) {
            if ($this->isForeignKeyConstraintViolation($e)) {
                // Could be invalid tag ID or post ID issues (though post ID is checked before usually)
                throw new InvalidArgumentException('Invalid tag reference.');
            }
            throw $e;
        }
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        // 23505 (Postgres) or 1062 (MySQL) or 23000 (SQLite/Generic)
        $sqlState = $e->errorInfo[0] ?? (string) $e->getCode();
        $errorCode = $e->errorInfo[1] ?? 0;

        if (in_array($sqlState, ['23505', '23000']) || $errorCode === 1062) {
            $message = strtolower($e->getMessage());

            // Check generic/common indicators
            if (str_contains($message, 'duplicate entry') ||
                str_contains($message, 'unique constraint') ||
                str_contains($message, 'posts_slug_unique')) {
                return true;
            }
        }

        return false;
    }

    private function isForeignKeyConstraintViolation(QueryException $e): bool
    {
        // 23503 (Postgres/Generic) or 1451/1452 (MySQL)
        $sqlState = $e->errorInfo[0] ?? (string) $e->getCode();
        $errorCode = $e->errorInfo[1] ?? 0;

        if ($sqlState === '23503') {
            return true;
        }

        // MySQL FK Error
        if (in_array($errorCode, [1451, 1452])) {
            return true;
        }

        // SQLite FK error often 23000 with specific message, but usually 23503 if extended result codes used
        // or just strict check on 23503 which is standard ANSI SQL state for FK violation

        return false;
    }
}

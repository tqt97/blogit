<?php

declare(strict_types=1);

namespace Modules\Post\Infrastructure\Persistence\Eloquent\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Post\Database\Factories\PostModelFactory;
use Modules\Post\Domain\ValueObjects\PostStatus;

final class PostModel extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'views_count',
        'comments_count',
        'likes_count',
        'published_at',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'published_at' => 'datetime',
        'views_count' => 'integer',
        'comments_count' => 'integer',
        'likes_count' => 'integer',
    ];

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopePublished($query): void
    {
        $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeSearch($query, ?string $term): void
    {
        if (empty($term)) {
            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%");
        });
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     */
    public function scopeByTag($query, ?int $tagId): void
    {
        if (! $tagId) {
            return;
        }

        $query->whereIn('id', function ($q) use ($tagId) {
            $q->select('post_id')
                ->from('post_tag')
                ->where('tag_id', $tagId);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function newFactory()
    {
        return PostModelFactory::new();
    }
}

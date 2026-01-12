<?php

namespace Modules\Post\Infrastructure\Persistence\Eloquent\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Post\Domain\ValueObjects\PostStatus;

class PostModel extends Model
{
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

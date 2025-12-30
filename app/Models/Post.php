<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'status',
        'published_at',
        'likes_count',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'likes_count' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    protected function applyPublishedConstraint(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function published(Builder $query): Builder
    {
        return $this->applyPublishedConstraint($query);
    }

    #[\Illuminate\Database\Eloquent\Attributes\Scope]
    protected function latestPublished(Builder $query): Builder
    {
        return $this
            ->applyPublishedConstraint($query)
            ->latest('published_at')
            ->orderByDesc('id');
    }
}

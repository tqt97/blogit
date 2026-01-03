<?php

namespace Modules\Tag\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Post\Models\Post;

class TagModel extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $table = 'tags';
    protected $fillable = [
        'name',
        'slug',
    ];

    // public function posts(): BelongsToMany
    // {
    //     return $this->belongsToMany(Post::class, 'post_tag')->withTimestamps();
    // }
}

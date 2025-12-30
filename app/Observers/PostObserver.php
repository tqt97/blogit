<?php

namespace App\Observers;

use App\Models\Like;
use App\Models\Post;

class PostObserver
{
    public function forceDeleted(Post $post): void
    {
        Like::query()
            ->where('likeable_type', $post->getMorphClass()) // 'post'
            ->where('likeable_id', $post->getKey())
            ->delete();
    }
}

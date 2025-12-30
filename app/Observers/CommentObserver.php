<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\Like;

class CommentObserver
{
    public function forceDeleted(Comment $comment): void
    {
        Like::query()
            ->where('likeable_type', $comment->getMorphClass()) // 'comment'
            ->where('likeable_id', $comment->getKey())
            ->delete();
    }
}

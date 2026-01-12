<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Events;

use Modules\Post\Domain\ValueObjects\PostId;
use Modules\Post\Domain\ValueObjects\PostSlug;
use Modules\Post\Domain\ValueObjects\PostTitle;

final readonly class PostUpdated
{
    public function __construct(public PostId $id, public PostTitle $title, PostSlug $slug) {}
}

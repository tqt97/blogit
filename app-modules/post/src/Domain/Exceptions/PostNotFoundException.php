<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Exceptions;

use DomainException;
use Throwable;

final class PostNotFoundException extends DomainException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('Post not found.', 0, $previous);
    }
}

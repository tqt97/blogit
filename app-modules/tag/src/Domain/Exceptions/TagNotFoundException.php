<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Exceptions;

use DomainException;
use Throwable;

final class TagNotFoundException extends DomainException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('Tag not found.', 0, $previous);
    }
}

<?php

declare(strict_types=1);

namespace Modules\Post\Domain\Exceptions;

use DomainException;
use Throwable;

final class PostInUseException extends DomainException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('Cannot delete post because it is in use.', 0, $previous);
    }
}

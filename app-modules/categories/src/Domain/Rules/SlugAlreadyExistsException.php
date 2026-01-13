<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\Exceptions;

use DomainException;
use Throwable;

final class SlugAlreadyExistsException extends DomainException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('Slug already exists.', 0, $previous);
    }
}

<?php

declare(strict_types=1);

namespace Modules\Categories\Domain\Exceptions;

use DomainException;
use Throwable;

final class CategoryNotFoundException extends DomainException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('Category not found.', 0, $previous);
    }
}

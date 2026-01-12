<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Exceptions;

use DomainException;
use Throwable;

final class TagInUseException extends DomainException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('Cannot delete tag because it is in use.', 0, $previous);
    }
}

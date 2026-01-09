<?php

declare(strict_types=1);

namespace Modules\Tag\Domain\Exceptions;

use DomainException;

final class SlugAlreadyExistsException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Slug already exists.');
    }
}

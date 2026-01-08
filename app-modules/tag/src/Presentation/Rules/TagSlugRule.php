<?php

declare(strict_types=1);

namespace Modules\Tag\Presentation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Tag\Domain\ValueObjects\TagSlug;

final class TagSlugRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        try {
            new TagSlug($value);
        } catch (\InvalidArgumentException $e) {
            $fail($e->getMessage());
        }
    }
}

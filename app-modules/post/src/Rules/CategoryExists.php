<?php

namespace Modules\Post\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Shared\Contracts\Taxonomy\CategoryLookup;

class CategoryExists implements ValidationRule
{
    public function __construct(
        private readonly CategoryLookup $lookup
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;  // nullable
        }

        if (! is_numeric($value) || (int) $value <= 0) {
            $fail('Invalid category.');

            return;
        }

        if (! $this->lookup->exists((int) $value)) {
            $fail('Category does not exist.');
        }
    }
}

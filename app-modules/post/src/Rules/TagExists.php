<?php

namespace Modules\Post\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Shared\Contracts\Taxonomy\TagLookup;

class TagExists implements ValidationRule
{
    public function __construct(
        private readonly TagLookup $lookup
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null) {
            return;
        }

        if (! is_array($value)) {
            $fail('Tags must be an array.');

            return;
        }

        $ids = array_values(array_unique(array_map('intval', $value)));
        if (empty($ids)) {
            return;
        }

        $valid = $this->lookup->filterExistingIds($ids);

        // Nếu muốn strict: có id nào không hợp lệ thì fail
        if (count($valid) !== count($ids)) {
            $fail('One or more tags do not exist.');
        }
    }
}

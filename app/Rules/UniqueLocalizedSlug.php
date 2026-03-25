<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueLocalizedSlug implements ValidationRule
{
    public function __construct(
        protected string $modelClass,
        protected ?int $ignoreId = null
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            return;
        }

        $locale = str_replace('slug.', '', $attribute);
        $query = $this->modelClass::query()->where("slug->{$locale}", $value);

        if ($this->ignoreId !== null) {
            $query->whereKeyNot($this->ignoreId);
        }

        if ($query->exists()) {
            $fail("The slug for locale {$locale} is already taken.");
        }
    }
}

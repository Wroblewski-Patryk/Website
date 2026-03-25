<?php

namespace App\Rules;

use App\Support\ReservedRouteSegments;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotReservedRouteSlug implements ValidationRule
{
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

        $slug = strtolower(trim($value));
        if (in_array($slug, ReservedRouteSegments::all(), true)) {
            $fail("The slug '{$slug}' is reserved by system routes.");
        }
    }
}

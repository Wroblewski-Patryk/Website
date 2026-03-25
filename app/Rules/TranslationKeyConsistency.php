<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TranslationKeyConsistency implements ValidationRule
{
    public function __construct(
        protected readonly string $group
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute format is invalid.');
            return;
        }

        $key = trim($value);

        if ($key === '' || str_contains($key, '..') || str_starts_with($key, '.') || str_ends_with($key, '.')) {
            $fail('The :attribute must use dot-separated namespaces (example: section.item).');
            return;
        }

        $segments = explode('.', $key);

        if (count($segments) < 2) {
            $fail('The :attribute must include a namespace and key segment (example: section.item).');
            return;
        }

        foreach ($segments as $segment) {
            if (!preg_match('/^[A-Za-z0-9_-]+$/', $segment)) {
                $fail('The :attribute contains invalid namespace characters.');
                return;
            }
        }

        if ($this->group === 'admin' && str_starts_with($key, 'admin.')) {
            $fail('The admin translation key should not start with "admin." because the group already defines the namespace.');
            return;
        }

        if ($this->group !== 'admin' && str_starts_with($key, 'admin.')) {
            $fail('Keys starting with "admin." must be stored in the admin group.');
        }
    }
}

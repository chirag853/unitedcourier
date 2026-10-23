<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NameContainsLetter implements ValidationRule
{
    public function __construct(private readonly string $label) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        if (preg_match('/\p{L}/u', $value) !== 1) {
            $fail($this->label.' must include at least one letter; it cannot be only a phone number.');
        }
    }
}

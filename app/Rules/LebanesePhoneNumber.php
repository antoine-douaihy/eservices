<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates Lebanese phone numbers in any common format:
 *   +961 70 123 456 / 961701234561 / 70 123 456 / 03 123 456 / 01 234 567
 * Accepts an optional +961 / 961 / 0 prefix followed by a valid
 * Lebanese mobile or landline subscriber number (7-8 digits).
 */
class LebanesePhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a valid phone number.');
            return;
        }

        // Strip spaces, dashes, and parentheses for matching
        $normalized = preg_replace('/[\s\-\(\)]+/', '', $value);

        // Lebanese numbers: optional +961/00961/961 country code, optional leading 0,
        // then a 7-8 digit subscriber number starting with a valid Lebanese prefix
        // (mobile: 3,70,71,76,78,79,81; landline area codes: 1,4,5,6,7,8,9)
        $pattern = '/^(?:\+?961|00961)?0?(3\d{6}|7[01689]\d{6}|81\d{6}|[1456789]\d{6,7})$/';

        if (!preg_match($pattern, $normalized)) {
            $fail('The :attribute must be a valid Lebanese phone number (e.g. +961 70 123 456).');
        }
    }
}

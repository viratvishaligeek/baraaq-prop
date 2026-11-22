<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeLink implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! self::isValid($value)) {
            $fail('The '.$attribute.' must be a safe web URL, relative path, fragment, email or telephone link.');
        }
    }

    public static function isValid(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $value = trim($value);
        if ($value === '' || preg_match('/[\r\n\\\\]/', $value)) {
            return false;
        }

        if (preg_match('~^https?://~i', $value)) {
            $parts = parse_url($value);

            return is_array($parts) && ! empty($parts['host']);
        }

        if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
            return true;
        }

        if (str_starts_with($value, '#')) {
            return true;
        }

        if (str_starts_with(strtolower($value), 'mailto:')) {
            return filter_var(substr($value, 7), FILTER_VALIDATE_EMAIL) !== false;
        }

        if (str_starts_with(strtolower($value), 'tel:')) {
            return (bool) preg_match('/^\+?[0-9().\-\s]{5,30}$/', substr($value, 4));
        }

        return false;
    }

    public static function sanitize(mixed $value, string $fallback = '#'): string
    {
        if (! is_string($value) || trim($value) === '' || ! self::isValid($value)) {
            return $fallback;
        }

        return trim($value);
    }
}

<?php

namespace Further\Mailmatch\Normalizers\Validation\Rules;

use Further\Mailmatch\Normalizers\Validation\Contracts\Rule;

class IsRequired implements Rule
{
    public static function message($attribute): string
    {
        return sprintf('`%s` is required.', $attribute);
    }

    public static function validate($value): bool
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) < 1) {
            return false;
        }

        return true;
    }
}
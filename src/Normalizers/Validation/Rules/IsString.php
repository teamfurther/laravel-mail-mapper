<?php

namespace Further\Mailmatch\Normalizers\Validation\Rules;

use Further\Mailmatch\Normalizers\Validation\Contracts\Rule;

class IsString implements Rule
{
    public static function message($attribute): string
    {
        return sprintf('`%s` must be a string.', $attribute);
    }

    public static function validate($value): bool
    {
        return is_string($value) || is_null($value);
    }
}
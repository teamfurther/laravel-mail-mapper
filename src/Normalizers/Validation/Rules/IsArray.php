<?php

namespace Further\Mailmatch\Normalizers\Validation\Rules;

use Further\Mailmatch\Normalizers\Validation\Contracts\Rule;

class IsArray implements Rule
{
    public static function message($attribute): string
    {
        return sprintf('`%s` must be an array.', $attribute);
    }

    public static function validate($value): bool
    {
        return is_array($value) || is_null($value);
    }
}
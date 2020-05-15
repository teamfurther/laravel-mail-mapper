<?php

namespace Further\Mailmatch\Normalizers\Validation\Rules;

use Further\Mailmatch\Normalizers\Validation\Contracts\Rule;

class IsEmail implements Rule
{
    public static function message($attribute): string
    {
        return sprintf('`%s` must be an email address.', $attribute);
    }

    public static function validate($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) || is_null($value);
    }
}
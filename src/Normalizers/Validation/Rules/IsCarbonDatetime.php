<?php

namespace Further\Mailmatch\Normalizers\Validation\Rules;

use Carbon\Carbon;
use Further\Mailmatch\Normalizers\Validation\Contracts\Rule;

class IsCarbonDatetime implements Rule
{
    public static function message($attribute): string
    {
        return sprintf('`%s` must be an instance of Carbon\Carbon.', $attribute);
    }

    public static function validate($value): bool
    {
        return $value instanceof Carbon || is_null($value);
    }
}
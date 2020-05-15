<?php

namespace Further\Mailmatch\Normalizers\Validation\Contracts;

interface Rule
{
    public static function message($attribute): string;

    public static function validate($value): bool;
}
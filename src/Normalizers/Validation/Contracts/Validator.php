<?php

namespace Further\Mailmatch\Normalizers\Validation\Contracts;

use Illuminate\Support\MessageBag;

interface Validator
{
    public function errors(): MessageBag;

    public function rules(): array;

    public function validate(): bool;
}
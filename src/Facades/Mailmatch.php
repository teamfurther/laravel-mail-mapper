<?php

namespace Further\Mailmatch\Facades;

use Illuminate\Support\Facades\Facade;

class Mailmatch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mailmatch';
    }
}
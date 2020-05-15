<?php

namespace Further\Mailmatch\Drivers;

use Illuminate\Mail\Events\MessageSent;

class Log implements DriverInterface
{
    public function register()
    {
        app('events')->listen(MessageSent::class, [$this, 'store']);
    }

    public function store(MessageSent $event)
    {
        if (config('mail.default') !== 'log') {
            return;
        }

        // normalize

    }

    public function sync()
    {

    }
}
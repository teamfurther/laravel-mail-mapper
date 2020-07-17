<?php

namespace Further\Mailmatch;

use Further\Mailmatch\Drivers\Google;
use Further\Mailmatch\Drivers\Log;
use Illuminate\Support\Manager;

class MailmatchManager extends Manager
{
    public function createGoogleDriver()
    {
        return new Google;
    }

    public function createLogDriver()
    {
        return new Log;
    }

    public function createMailgunDriver()
    {
//        return new Mailgun;
    }

    public function getDefaultDriver()
    {
        return $this->container['config']['mailmatch.driver'];
    }
}
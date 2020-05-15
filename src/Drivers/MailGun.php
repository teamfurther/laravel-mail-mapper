<?php


namespace Further\Mailmatch\Drivers;

use Mailgun\Mailgun as MailGunDriver;


class MailGun implements DriverInterface
{
    public function getEmails()
    {
        $client = $this->getClient();
        $expression   = 'match_recipient(".*@mg.example.com")';
        $actions      = array('forward("my_address@example.com")', 'stop()');
        $description  = 'Catch All and Forward';
        $result = $client->routes()->create('', $actions, $description);

        dd($result);
    }

    public function getClient(): MailGunDriver
    {
        if (config('mailmatch.services.mailgun.private_api_key') == '' | config('mailmatch.services.mailgun.end_point') == '') {
            throw new \Exception('You must provide the Mailgun Private_api_key and End_point first!');
        }

        return MailGunDriver::create(config('mailmatch.services.mailgun.private_api_key'), config('mailmatch.services.mailgun.end_point'));
    }
}

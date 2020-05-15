<?php

namespace Further\Mailmatch\Drivers;

use Further\Mailmatch\Normalizers\MessageNormalizer;
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

        try {
            $normalizedMessage = (new MessageNormalizer())
                ->setBcc($event->message->getBcc())
                ->setCcRecipients($event->message->getCc())
                ->setDatetime($event->message->getDate())
                ->setFrom($event->message->getSender())
                ->setHtml($event->message->getBody())
                ->setSubject($event->message->getSubject())
                ->setToRecipients($event->message->getTo())
                ->save();

            dd($normalizedMessage);
        } catch (\Exception $exception) {
            print $exception->getMessage();
        }
    }

    public function sync()
    {

    }
}
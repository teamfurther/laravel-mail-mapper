<?php

namespace Further\Mailmatch\Drivers;

use Carbon\Carbon;
use Further\Mailmatch\Models\Message;
use Further\Mailmatch\Normalizers\MessageNormalizer;
use Illuminate\Mail\Events\MessageSent;

class Log implements DriverInterface
{
    public function register()
    {
        app('events')->listen(MessageSent::class, [$this, 'store']);
    }

    /**
     * @param MessageSent $event
     */
    public function store($event)
    {
        if (config('mail.default') !== 'log') {
            return;
        }

        try {
            $ccRecipients = [];
            $recipients = [];

            if ($event->message->getCc()) {
                foreach ($event->message->getCc() as $email => $name) {
                    $ccRecipients[] = [
                        'email' => $email,
                        'name' => $name
                    ];
                }
            }

            if ($event->message->getTo()) {
                foreach ($event->message->getTo() as $email => $name) {
                    $recipients[] = [
                        'email' => $email,
                        'name' => $name
                    ];
                }
            }

            $normalizedMessage = (new MessageNormalizer())
                ->setBcc(array_keys($event->message->getBcc())[0])
                ->setBccName(array_values($event->message->getBcc())[0])
                ->setCcRecipients($ccRecipients)
                ->setDatetime(Carbon::parse($event->message->getDate()))
                ->setFrom(array_keys($event->message->getFrom())[0])
                ->setFromName(array_values($event->message->getFrom())[0])
                ->setHtml($event->message->getBody())
                ->setSubject($event->message->getSubject())
                ->setToRecipients($recipients)
                ->save();

            Message::createFromMessage($normalizedMessage);
        } catch (\Exception $exception) {
            print $exception->getMessage();
        }
    }

    public function sync()
    {
        // TODO: Implement sync() method.
    }
}

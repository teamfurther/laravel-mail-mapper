<?php

namespace Further\Mailmatch\Drivers;

use Carbon\Carbon;
use Further\Mailmatch\Models\Message;
use Further\Mailmatch\Normalizers\MessageNormalizer;
use Further\Mailmatch\Services\LogService;
use Illuminate\Mail\Events\MessageSent;

class Log implements DriverInterface
{
    /**
     * @TODO: switch to type-hinting once support for PHP7.3 is over
     *
     * @var LogService
     */
    private $logService;

    public function __construct()
    {
        $this->logService = resolve('Further\Mailmatch\Services\LogService');
    }

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
            // normalize message
            $normalizedMessage = (new MessageNormalizer())
                ->setBcc($this->logService->getBccFieldFromMessage($event->message))
                ->setBccName($this->logService->getBccNameFieldFromMessage($event->message))
                ->setCcRecipients($this->logService->getCCRecipientsArrayFromMessage($event->message))
                ->setDatetime(Carbon::parse($event->message->getDate()))
                ->setFrom($this->logService->getFromFieldFromMessage($event->message))
                ->setFromName($this->logService->getFromNameFieldFromMessage($event->message))
                ->setHtml($event->message->getBody())
                ->setSubject($event->message->getSubject())
                ->setToRecipients($this->logService->getRecipientsArrayFromMessage($event->message))
                ->save();

            // create and store message
            Message::createFromMessage($normalizedMessage);
        } catch (\Exception $exception) {
            print $exception->getMessage();
        }
    }
}
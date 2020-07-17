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
     * @param MessageSent $message
     */
    public function store($message)
    {
        if (config('mail.default') !== 'log') {
            return;
        }

        try {
            // normalize message
            $normalizedMessage = (new MessageNormalizer())
                ->setBcc($this->logService->getBccFieldFromMessage($message->message))
                ->setBccName($this->logService->getBccNameFieldFromMessage($message->message))
                ->setCcRecipients($this->logService->getCCRecipientsArrayFromMessage($message->message))
                ->setDatetime(Carbon::parse($message->message->getDate()))
                ->setFrom($this->logService->getFromFieldFromMessage($message->message))
                ->setFromName($this->logService->getFromNameFieldFromMessage($message->message))
                ->setHtml($message->message->getBody())
                ->setSubject($message->message->getSubject())
                ->setToRecipients($this->logService->getRecipientsArrayFromMessage($message->message))
                ->save();

            // create and store message
            Message::createFromMessage($normalizedMessage);
        } catch (\Exception $exception) {
            print $exception->getMessage();
        }
    }

    public function sync()
    {
        //
    }
}

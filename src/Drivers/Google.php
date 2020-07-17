<?php

namespace Further\Mailmatch\Drivers;

use Carbon\Carbon;
use Further\Mailmatch\Models\Message;
use Further\Mailmatch\Normalizers\MessageNormalizer;
use Further\Mailmatch\Services\GoogleService;

class Google implements DriverInterface
{
    /**
     * @var GoogleService
     */
    private $googleService;

    public function __construct()
    {
        $this->googleService = resolve(GoogleService::class);
    }

    public function register()
    {
        echo 'Google';
    }

    public function store($message)
    {

    }

    public function sync()
    {
        try {
            $emails = $this->googleService->getEmails();

            foreach ($emails as $email) {
                $normalizedMessage = (new MessageNormalizer())
                    ->setBcc($email['bcc'])
                    ->setBccName($email['bccName'])
                    ->setCcRecipients($email['ccRecipients'])
                    ->setDatetime(Carbon::parse($email['dateTime']))
                    ->setFrom($email['from'])
                    ->setFromName($email['fromName'])
                    ->setHtml($email['html'])
                    ->setSubject($email['to'])
                    ->setToRecipients($email['recipients'])
                    ->save();

                Message::createFromMessage($normalizedMessage);
            }
        } catch (\Exception $exception) {
            print $exception->getMessage();
        }
    }
}

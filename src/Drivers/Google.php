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
                    ->setBcc($this->googleService->getBccFieldFromMessage($email))
                    ->setBccName($this->googleService->getBccNameFieldFromMessage($email))
                    ->setCcRecipients($this->googleService->getCCRecipientsArrayFromMessage($email))
                    ->setDatetime(Carbon::parse($email['dateTime']))
                    ->setFrom($this->googleService->getFromFieldFromMessage($email))
                    ->setFromName($this->googleService->getFromNameFieldFromMessage($email))
                    ->setHtml($this->googleService->getHtmlFieldFromMessage($email))
                    ->setSubject($this->googleService->getSubjectFieldFromMessage($email))
                    ->setToRecipients($this->googleService->getRecipientsArrayFromMessage($email))
                    ->save();

                Message::createFromMessage($normalizedMessage);
            }
        } catch (\Exception $exception) {
            print $exception->getMessage();
        }
    }
}

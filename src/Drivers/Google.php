<?php

namespace Further\Mailmatch\Drivers;

use Carbon\Carbon;
use Further\Mailmatch\Actions\GetMailboxesAction;
use Further\Mailmatch\Models\Message;
use Further\Mailmatch\Normalizers\MessageNormalizer;
use Further\Mailmatch\Services\GoogleService;

class Google implements DriverInterface
{
    private GoogleService $googleService;
    private GetMailboxesAction $getMailboxesAction;

    public function __construct()
    {
        $this->googleService = resolve(GoogleService::class);
        $this->getMailboxesAction = resolve(GetMailboxesAction::class);
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
        foreach ($this->getMailboxesAction->execute() as $mailboxKey) {
            try {
                $emails = $this->googleService->getEmails($mailboxKey);

                foreach ($emails as $email) {
                    $normalizedMessage = (new MessageNormalizer())
                        ->setBcc($this->googleService->getBccFieldFromMessage($email))
                        ->setBccName($this->googleService->getBccNameFieldFromMessage($email))
                        ->setCcRecipients($this->googleService->getCCRecipientsArrayFromMessage($email))
                        ->setDatetime(Carbon::parse($email['dateTime']))
                        ->setFrom($this->googleService->getFromFieldFromMessage($email))
                        ->setFromName($this->googleService->getFromNameFieldFromMessage($email))
                        ->setHtml($this->googleService->getHtmlFieldFromMessage($email))
                        ->setMailbox($mailboxKey)
                        ->setPlainText($this->googleService->getPlainTextFieldFromMessage($email))
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
}

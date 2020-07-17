<?php

namespace Further\Mailmatch\Services;

class LogService
{
    public function getBccFieldFromMessage(\Swift_Message $message): ?string
    {
        $bcc = $message->getBcc();

        return $bcc ? array_keys($bcc)[0] : null;
    }

    public function getBccNameFieldFromMessage(\Swift_Message $message): ?string
    {
        $bcc = $message->getBcc();

        return $bcc ? array_values($bcc)[0] : null;
    }

    public function getCCRecipientsArrayFromMessage(\Swift_Message $message): ?array
    {
        $ccRecipients = $message->getCc();

        if (!$ccRecipients) {
            return null;
        }

        $result = [];
        foreach ($ccRecipients as $email => $name) {
            $result[] = [
                'email' => $email,
                'name' => $name
            ];
        }

        return $result;
    }

    public function getFromFieldFromMessage(\Swift_Message $message): ?string
    {
        $from = $message->getFrom();

        return $from ? array_keys($from)[0] : null;
    }

    public function getFromNameFieldFromMessage(\Swift_Message $message): ?string
    {
        $from = $message->getFrom();

        return $from ? array_values($from)[0] : null;
    }

    public function getRecipientsArrayFromMessage(\Swift_Message $message): ?array
    {
        $recipients = $message->getTo();

        if (!$recipients) {
            return null;
        }

        $result = [];
        foreach ($recipients as $email => $name) {
            $result[] = [
                'email' => $email,
                'name' => $name
            ];
        }

        return $result;
    }
}
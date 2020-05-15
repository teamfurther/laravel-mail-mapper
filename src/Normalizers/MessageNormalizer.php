<?php

namespace Further\Mailmatch\Normalizers;

use Further\Mailmatch\Normalizers\Validation\Validators\NormalizedMessageValidator;

class MessageNormalizer
{
    private $attachments;
    private $bcc;
    private $bccName;
    private $ccRecipients;
    private $datetime;
    private $from;
    private $fromName;
    private $html;
    private $plainText;
    private $subject;
    private $toRecipients;

    /**
     * @throws \Exception
     */
    public function save(): array
    {
        $data = $this->toArray();

        $validator = new NormalizedMessageValidator($data);

        if (!$validator->validate()) {
            throw new \Exception($validator->errors()->first());
        }

        return $data;
    }

    public function setAttachments($attachments): MessageNormalizer
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function setBcc($bcc): MessageNormalizer
    {
        $this->bcc = $bcc;

        return $this;
    }

    public function setBccName($bccName): MessageNormalizer
    {
        $this->bccName = $bccName;

        return $this;
    }

    public function setCcRecipients($ccRecipients): MessageNormalizer
    {
        $this->ccRecipients = $ccRecipients;

        return $this;
    }

    public function setDatetime($datetime): MessageNormalizer
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function setFrom($from): MessageNormalizer
    {
        $this->from = $from;

        return $this;
    }

    public function setFromName($fromName): MessageNormalizer
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function setHtml($html): MessageNormalizer
    {
        $this->html = $html;

        return $this;
    }

    public function setPlainText($plainText): MessageNormalizer
    {
        $this->plainText = $plainText;

        return $this;
    }

    public function setSubject($subject): MessageNormalizer
    {
        $this->subject = $subject;

        return $this;
    }

    public function setToRecipients($toRecipients): MessageNormalizer
    {
        $this->toRecipients = $toRecipients;

        return $this;
    }

    private function toArray(): array
    {
        return [
            'attachments' => $this->attachments,
            'bcc' => $this->bcc,
            'bccName' => $this->bccName,
            'ccRecipients' => $this->ccRecipients,
            'datetime' => $this->datetime,
            'from' => $this->from,
            'fromName' => $this->fromName,
            'html' => $this->html,
            'plainText' => $this->plainText,
            'subject' => $this->subject,
            'toRecipients' => $this->toRecipients,
        ];
    }
}
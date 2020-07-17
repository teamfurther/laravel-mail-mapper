<?php

namespace Further\Mailmatch\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $dates = [
        'datetime',
    ];

    protected $fillable = [
        'bcc',
        'bcc_name',
        'datetime',
        'from',
        'from_name',
        'html',
        'mailbox_key',
        'plain_text',
        'subject',
    ];

    protected $table = 'mailmatch_messages';

    public function attachments()
    {
        return $this->hasMany('Further\Mailmatch\Models\MessageAttachment', 'message_id');
    }

    public function cc()
    {
        return $this->hasMany('Further\Mailmatch\Models\MessageRecipient', 'message_id')->where('type', 'cc');
    }

    public static function createFromMessage($message)
    {
        $messageObject = self::create([
            'bcc' => $message['bcc'],
            'bcc_name' => $message['bccName'],
            'datetime' => $message['datetime'],
            'from' => $message['from'],
            'from_name' => $message['fromName'],
            'html' => $message['html'],
            'mailbox_key' => $message['mailbox'],
            'plain_text' => $message['plainText'],
            'subject' => $message['subject'],
        ]);

        $toRecipients = array_map(function ($item) {
            $item['type'] = 'to';
            return $item;
        }, $message['toRecipients']);
        $messageObject->to()->createMany($toRecipients);

        if ($message['ccRecipients']) {
            $ccRecipients = array_map(function ($item) {
                $item['type'] = 'cc';
                return $item;
            }, $message['ccRecipients']);
            $messageObject->to()->createMany($ccRecipients);
        }

        return $messageObject;
    }

    public function relations()
    {
        return $this->hasMany('Further\Mailmatch\Models\MessageRelation', 'message_id');
    }

    public function to()
    {
        return $this->hasMany('Further\Mailmatch\Models\MessageRecipient', 'message_id')->where('type', 'to');
    }
}

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
        return $this->hasMany('Further\Mailmatch\Models\MessageRecipients', 'message_id')->where('type', 'cc');
    }

    public static function createFromMessage($message)
    {

    }

    public function relations()
    {
        return $this->hasMany('Further\Mailmatch\Models\MessageRelation', 'message_id');
    }

    public function to()
    {
        return $this->hasMany('Further\Mailmatch\Models\MessageRecipients', 'message_id')->where('type', 'to');
    }
}

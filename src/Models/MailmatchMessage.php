<?php

namespace Further\Mailmatch\Models;

use Illuminate\Database\Eloquent\Model;

class MailmatchMessage extends Model
{
    protected $dates = [
        'date',
    ];

    protected $fillable = [
        'bcc',
        'body',
        'cc',
        'date',
        'from',
        'mailbox_key',
        'plain_text_body',
        'subject',
        'to',
    ];

    protected $table = 'mailmatch_messages';

    public function attachments()
    {
        return $this->hasMany('Further\Mailmatch\Models\MailmatchAttachment', 'message_id');
    }

    public function relations()
    {
        return $this->hasMany('Further\Mailmatch\Models\MailmatchRelations', 'message_id');
    }

}

<?php

namespace Further\Mailmatch\Models;

use Illuminate\Database\Eloquent\Model;

class MailmatchAttachment extends Model
{
    protected $fillable = [
        'file',
        'message_id',
    ];

    protected $table = 'mailmatch_attachments';
}

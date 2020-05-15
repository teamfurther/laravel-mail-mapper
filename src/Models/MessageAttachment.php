<?php

namespace Further\Mailmatch\Models;

use Illuminate\Database\Eloquent\Model;

class MessageAttachment extends Model
{
    protected $fillable = [
        'file',
        'message_id',
    ];

    protected $table = 'mailmatch_message_attachments';
}

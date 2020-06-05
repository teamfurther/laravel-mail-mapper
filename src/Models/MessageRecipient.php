<?php

namespace Further\Mailmatch\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{
    protected $fillable = [
        'email',
        'message_id',
        'name',
        'type',
    ];

    protected $table = 'mailmatch_message_recipients';

    public $timestamps = false;
}

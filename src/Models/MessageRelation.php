<?php

namespace Further\Mailmatch\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRelation extends Model
{
    protected $fillable = [
        'message_id',
        'owner',
        'owner_id',
    ];

    protected $table = 'mailmatch_message_relations';

    public $timestamps = false;
}

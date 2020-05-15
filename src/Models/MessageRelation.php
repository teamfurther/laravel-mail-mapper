<?php

namespace Further\Mailmatch\Models;

use Illuminate\Database\Eloquent\Model;

class MessageRelation extends Model
{
    protected $fillable = [
        'message_id',
        'model_primary_key',
    ];

    protected $table = 'mailmatch_message_relations';
}

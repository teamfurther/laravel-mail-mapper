<?php

namespace Further\Mailmatch\Contracts;

use Further\Mailmatch\Models\Message;
use Illuminate\Database\Eloquent\Model;

interface RelationshipRule
{
    public function handle(Message $message, Model $owner);
}
<?php

namespace Further\Mailmatch\Actions;

class GetMailboxesAction
{
    /**
     * @return string[]
     */
    public function execute(): array
    {
        return array_keys(config('mailmatch.mailboxes'));
    }
}

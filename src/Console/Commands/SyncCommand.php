<?php

namespace Further\Mailmatch\Console\Commands;

use Further\Mailmatch\Facades\Mailmatch;
use Illuminate\Console\Command;

class SyncCommand extends Command
{
    /**
     * The console command description.
     */
    protected $description = 'Syncs email messages to database';

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mailmatch:sync';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Mailmatch::sync();
    }
}

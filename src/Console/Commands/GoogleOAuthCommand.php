<?php

namespace Further\Mailmatch\Console\Commands;

use Further\Mailmatch\Actions\GetMailboxesAction;
use Further\Mailmatch\Services\GoogleService;
use Illuminate\Console\Command;

class GoogleOAuthCommand extends Command
{
    /**
     * The console command description.
     */
    protected $description = 'Generates the OAuth token to used by the Gmail API';

    protected GoogleService $googleService;

    protected GetMailboxesAction $getMailboxesAction;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mailmatch:generate-google-oauth-token {key : Key of your mailbox, as defined in mailmatch config.}';

    public function __construct(GoogleService $google, GetMailboxesAction $getMailboxesAction)
    {
        parent::__construct();

        $this->googleService = $google;
        $this->getMailboxesAction = $getMailboxesAction;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->argument('key');

        if (!in_array($key, $this->getMailboxesAction->execute())) {
            $this->error('The key is not exists in mailmatch configuration.');

            return 1;
        }

        try {
            $this->googleService->getClient($key);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $this->info('Authentication was successful.');

        return 0;
    }
}

<?php

namespace Further\Mailmatch\Console\Commands;

use Further\Mailmatch\Drivers\Google;
use Illuminate\Console\Command;

class GoogleOAuthCommand extends Command
{
    /**
     * The console command description.
     */
    protected $description = 'Generates the OAuth token to used by the Gmail API';

    /**
     * @var Google
     */
    protected $google;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mailmatch:generate-google-oauth-token';

    /**
     * Create a new command instance.
     */
    public function __construct(Google $google)
    {
        parent::__construct();

        $this->google = $google;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->google->getClient();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $this->info('Authentication was successful.');

        return 0;
    }
}

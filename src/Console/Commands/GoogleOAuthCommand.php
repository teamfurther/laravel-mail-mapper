<?php

namespace Further\Mailmatch\Console\Commands;

use Further\Mailmatch\Services\GoogleService;
use Illuminate\Console\Command;

class GoogleOAuthCommand extends Command
{
    /**
     * The console command description.
     */
    protected $description = 'Generates the OAuth token to used by the Gmail API';

    /**
     * @var GoogleService
     */
    protected $googleService;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mailmatch:generate-google-oauth-token';

    /**
     * Create a new command instance.
     * @param GoogleService $google
     */
    public function __construct(GoogleService $google)
    {
        parent::__construct();

        $this->googleService = $google;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->googleService->getClient();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $this->info('Authentication was successful.');

        return 0;
    }
}

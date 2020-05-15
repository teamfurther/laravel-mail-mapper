<?php

namespace Further\Mailmatch\Console\Commands;

use Further\Mailmatch\Drivers\GoogleDriver;
use Illuminate\Console\Command;

class GoogleOAuthCommand extends Command
{
    /**
     * The console command description.
     */
    protected $description = 'Generates the OAuth token to used by the Gmail API';

    /**
     * @var GoogleDriver
     */
    protected $googleDriver;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mailmatch:generate-google-oauth-token';

    /**
     * Create a new command instance.
     */
    public function __construct(GoogleDriver $googleDriver)
    {
        parent::__construct();

        $this->googleDriver = $googleDriver;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->googleDriver->getClient();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return 1;
        }

        $this->info('Authentication was successfully.');

        return 0;
    }
}

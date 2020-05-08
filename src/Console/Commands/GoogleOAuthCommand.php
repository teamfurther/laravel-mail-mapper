<?php

namespace Further\Mailmatch\Console\Commands;

use Google_Client;
use Google_Service_Gmail;
use Illuminate\Console\Command;

class GoogleOAuthCommand extends Command
{
    /**
     * The console command description.
     */
    protected $description = 'Generates the OAuth token to used by the Gmail API.';

    /**
     * @var Google_Client
     */
    protected $googleClient;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mailmatch:generate-google-oauth-token';

    /**
     * Create a new command instance.
     */
    public function __construct(Google_Client $googleClient)
    {
        parent::__construct();

        $this->googleClient = $googleClient;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $client = $this->getClient($this->getConfig());
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return 'Authentication was successfully.';
    }

    /**
     * Get Google client.
     *
     * @param array $config
     * @return Google_Client
     * @throws \Google_Exception
     */
    private function getClient(array $config): Google_Client
    {
        $client = $this->googleClient;
        $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
        $client->setAuthConfig($config);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.
        $tokenPath = '../../../google-token.json';

        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($client->getRefreshToken()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                $client->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        }

        return $client;
    }

    /**
     * Get google client config array.
     *
     * @return array|array[]
     */
    private function getConfig(): array
    {
        return [
            'installed' => [
                'client_id' => env('GOOGLE_CLIENT_ID'),
                'auth_uri' => env('GOOGLE_AUTH_URI'),
                'token_uri' => env('GOOGLE_TOKEN_URI'),
                'auth_provider_x509_cert_url' => env('GOOGLE_AUTH_PROVIDER_X509_CERT_URL'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'redirect_uris' => [env('GOOGLE_REDIRECT_URI')]
            ]
        ];
    }
}

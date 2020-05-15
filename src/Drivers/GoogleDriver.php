<?php


namespace Further\Mailmatch\Drivers;


class GoogleDriver implements DriverInterface
{
    /**
     * Token file path.
     *
     * @var string
     */
    private const TOKEN_FILE = './vendor/teamfurther/laravel-mailmatch/google-token.json';

    /**
     * Generate google token.
     *
     * @param \Google_Client $client
     * @throws \Exception
     */
    private function generateToken(\Google_Client $client)
    {
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
                throw new \Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname(self::TOKEN_FILE))) {
            mkdir(dirname(self::TOKEN_FILE), 0700, true);
        }
        file_put_contents(self::TOKEN_FILE, json_encode($client->getAccessToken()));
    }

    /**
     * Get Google client.
     *
     * @return \Google_Client
     * @throws \Exception
     */
    public function getClient(): \Google_Client
    {
        $client = new \Google_Client();
        $client->setScopes(\Google_Service_Gmail::GMAIL_READONLY);
        $client->setAuthConfig($this->getConfig());
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.

        if ($this->getToken()) {
            $client->setAccessToken($this->getToken());
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
           $this->generateToken($client);
        }

        return $client;
    }

    /**
     * Get google client config array.
     *
     * @return array|array[]
     * @throws \Exception
     */
    private function getConfig(): array
    {
        if (config('mailmatch.services.google.client_secret') == '' || config('mailmatch.services.google.client_id') == '') {
            throw new \Exception('You must provide the Google Client_ID and Client_Secret first!');
        }

        return [
            'installed' => [
                'client_id' => config('mailmatch.services.google.client_id'),
                'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
                'token_uri' => 'https://oauth2.googleapis.com/token',
                'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
                'client_secret' => config('mailmatch.services.google.client_secret'),
                'redirect_uris' => ['urn:ietf:wg:oauth:2.0:oob']
            ]
        ];
    }

    /**
     * Get the token.
     *
     * @return mixed|null
     */
    private function getToken()
    {
        if (file_exists(self::TOKEN_FILE)) {
            return json_decode(file_get_contents(self::TOKEN_FILE), true);
        }

        return null;
    }
}

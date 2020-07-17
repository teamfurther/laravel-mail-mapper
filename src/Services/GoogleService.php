<?php

namespace Further\Mailmatch\Services;

use Exception;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_MessagePart;
use Google_Service_Gmail_MessagePartHeader;

class GoogleService
{
    /**
     * Token file path.
     *
     * @var string
     */
    private const TOKEN_PATH = './vendor/teamfurther/laravel-mailmatch/';

    private function convertEmailStringToArray(string $emailString): array
    {
        return [
            'email' => trim(preg_replace('/([^\<]+)<([^>]+)>/', '$2', $emailString)),
            'name' => trim(preg_replace('/([^\<]+)<([^>]+)>/', '$1', $emailString)),
        ];
    }

    private function decodeBody($body): string {
        $rawData = $body;
        $sanitizedData = strtr($rawData,'-_', '+/');
        $decodedMessage = base64_decode($sanitizedData);

        if (!$decodedMessage) {
            return '';
        }

        return $decodedMessage;
    }

    private function generateToken(Google_Client $client, string $key)
    {
        $tokenName = $key . '.json';

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
        if (!file_exists(dirname(self::TOKEN_PATH . $tokenName))) {
            mkdir(dirname(self::TOKEN_PATH . $tokenName), 0700, true);
        }
        file_put_contents(self::TOKEN_PATH . $tokenName, json_encode($client->getAccessToken()));
    }

    public function getBccFieldFromMessage(Google_Service_Gmail_MessagePart $message): ?string
    {
        /** @var Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getHeaders() as $header) {
            if ($header->getName() == 'Bcc') {
                return $this->convertEmailStringToArray($header->getValue())['email'];
            }
        }

        return null;
    }

    public function getBccNameFieldFromMessage(Google_Service_Gmail_MessagePart $message): ?string
    {
        /** @var Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getHeaders() as $header) {
            if ($header->getName() == 'Bcc') {
                return $this->convertEmailStringToArray($header->getValue())['name'];
            }
        }

        return null;
    }

    public function getCCRecipientsArrayFromMessage(Google_Service_Gmail_MessagePart $message): ?array
    {
        /** @var Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getHeaders() as $header) {
            if ($header->getName() == 'Cc') {
                $result = [];
                $recipients = explode(',', $header->getValue());

                foreach ($recipients as $recipient) {
                    $result[] = [
                        'email' => $this->convertEmailStringToArray($recipient)['email'],
                        'name' => $this->convertEmailStringToArray($recipient)['name'],
                    ];
                }

                return $result;
            }
        }

        return null;
    }

    public function getClient(string $key): Google_Client
    {
        $client = new Google_Client();
        $client->setScopes(Google_Service_Gmail::GMAIL_READONLY);
        $client->setAuthConfig($this->getConfig());
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Load previously authorized token from a file, if it exists.
        // The file token.json stores the user's access and refresh tokens, and is
        // created automatically when the authorization flow completes for the first
        // time.

        if ($this->getToken($key)) {
            $client->setAccessToken($this->getToken($key));
        }

        // If there is no previous token or it's expired.
        if ($client->isAccessTokenExpired()) {
            $this->generateToken($client, $key);
        }

        return $client;
    }

    private function getConfig(): array
    {
        if (
            config('mailmatch.services.google.client_secret') == ''
            || config('mailmatch.services.google.client_id') == ''
        ) {
            throw new Exception('Please provide the Google client_id and client_secret first!');
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
     * @return Google_Service_Gmail_MessagePart[]
     */
    public function getEmails(string $key): array
    {
        $client = $this->getClient($key);
        $service = new Google_Service_Gmail($client);
        $userId = 'me';
        $messages = $this->getListUsersMessages($service, $userId, ['q' => 'newer_than:1d']);
        $result = [];

        foreach ($messages as $message) {
            $result[] = $service->users_messages->get($userId, $message->getId())->getPayload();
        }

        return $result;
    }

    public function getFromFieldFromMessage(Google_Service_Gmail_MessagePart $message): ?string
    {
        /** @var Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getHeaders() as $header) {
            if ($header->getName() == 'From') {
                return $this->convertEmailStringToArray($header->getValue())['email'];
            }
        }

        return null;
    }

    public function getFromNameFieldFromMessage(Google_Service_Gmail_MessagePart $message): ?string
    {
        /** @var Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getHeaders() as $header) {
            if ($header->getName() == 'From') {
                return $this->convertEmailStringToArray($header->getValue())['name'];
            }
        }

        return null;
    }

    public function getHtmlFieldFromMessage(Google_Service_Gmail_MessagePart $message): ?string
    {
        foreach ($message->getParts() as $part) {
            /** @var Google_Service_Gmail_MessagePart $part */
            if (strpos($part->mimeType, 'html') !== false) {
                return $this->decodeBody($part->getBody()->data);
            } elseif (strpos($part->mimeType, 'multipart') !== false) {
                foreach ($part->getParts() as $p) {
                    if (strpos($p->mimeType, 'html') !== false) {
                        return $this->decodeBody($p->getBody()->data);
                    }
                }
            }
        }

        return null;
    }

    private function getListUsersMessages(Google_Service_Gmail $service, string $userId, array $opt): array
    {
        $messages = [];
        $pageToken = null;

        do {
            if ($pageToken) {
                $opt['pageToken'] = $pageToken;
            }

            $messagesResponse = $service->users_messages->listUsersMessages($userId, $opt);

            if ($messagesResponse->getMessages()) {
                $messages = array_merge($messages, $messagesResponse->getMessages());
                $pageToken = $messagesResponse->getNextPageToken();
            }
        } while ($pageToken);

        return $messages;
    }

    public function getPlainTextFieldFromMessage(Google_Service_Gmail_MessagePart $message): ?string
    {
        foreach ($message->getParts() as $part) {
            /** @var Google_Service_Gmail_MessagePart $part */
            if (strpos($part->mimeType, 'plain') !== false) {
                return $this->decodeBody($part->getBody()->data);
            }
        }

        return null;
    }

    public function getRecipientsArrayFromMessage(Google_Service_Gmail_MessagePart $message): ?array
    {
        /** @var Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getHeaders() as $header) {
            if ($header->getName() == 'To') {
                $result = [];
                $recipients = explode(',', $header->getValue());

                foreach ($recipients as $recipient) {
                    $result[] = [
                        'email' => $this->convertEmailStringToArray($recipient)['email'],
                        'name' => $this->convertEmailStringToArray($recipient)['name'],
                    ];
                }

                return $result;
            }
        }

        return null;
    }

    public function getSubjectFieldFromMessage(Google_Service_Gmail_MessagePart $message): ?string
    {
        /** @var Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getHeaders() as $header) {
            if ($header->getName() == 'Subject') {
                return $header->getValue();
            }
        }

        return null;
    }

    private function getToken(string $key)
    {
        $tokenName = $key . '.json';

        if (file_exists(self::TOKEN_PATH . $tokenName)) {
            return json_decode(file_get_contents(self::TOKEN_PATH . $tokenName), true);
        }

        return null;
    }
}

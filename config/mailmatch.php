<?php

return [

    /*
     * The driver to use when listening for incoming emails.
     *
     * Supported drivers: "log", "google", "mailgun"
     */
    'driver' => env('MAILMATCH_DRIVER', 'log'),

    /*
     *
     */
    'mailboxes' => [
        'my_mailbox' => [

        ]
    ],

    /*
     * Third-party service configuration.
     */
    'services' => [

        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        ],

        'mailgun' => [
            'key' => env('MAILGUN_KEY'),
        ],

    ],
];

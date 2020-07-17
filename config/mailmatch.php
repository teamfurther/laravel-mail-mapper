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
            'relationships' => [
                [
                    'name' => 'messages',
                    'owner' => \App\User::class,
                    'rule' => function($message, $owner) {
                        return $message->to === $owner->email || $message->from === $owner->email;
                    },
                ],
                [
                    'name' => 'activity',
                    'owner' => \App\Project::class,
                    'rule' => \App\Rules\ProjectActivityRule::class, // must extend Further\Mailmatch\Contracts\RelationshipRule
                ],
            ]
        ]
    ],

    /*
     * The model class.
     */
    'model' => \Further\Mailmatch\Models\Message::class,

    /*
     * Third-party service configuration.
     */
    'services' => [

        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'user' => 'me',
        ],

        'mailgun' => [
            'private_api_key' => env('MAILGUN_PRIVATE_API_KEY'),
            'end_point' => env('MAILGUN_ENDPOINT'),
        ],

    ],
];

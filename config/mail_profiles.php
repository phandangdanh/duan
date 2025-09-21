<?php

return [
    // Profile to use: gmail, mailtrap, log
    'default_profile' => env('MAIL_PROFILE', 'log'),

    'profiles' => [
        'gmail' => [
            'default' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => env('GMAIL_USERNAME', env('MAIL_USERNAME')),
            'password' => env('GMAIL_PASSWORD', env('MAIL_PASSWORD')),
            'from_address' => env('MAIL_FROM_ADDRESS', env('GMAIL_USERNAME')),
            'from_name' => env('MAIL_FROM_NAME', 'ThriftZone'),
        ],

        'mailtrap' => [
            'default' => 'smtp',
            'host' => 'sandbox.smtp.mailtrap.io',
            'port' => 2525,
            'encryption' => null,
            'username' => env('MAILTRAP_USERNAME'),
            'password' => env('MAILTRAP_PASSWORD'),
            'from_address' => env('MAIL_FROM_ADDRESS', 'no-reply@thriftzone.local'),
            'from_name' => env('MAIL_FROM_NAME', 'ThriftZone'),
        ],

        'log' => [
            'default' => 'log',
            'from_address' => env('MAIL_FROM_ADDRESS', 'no-reply@thriftzone.local'),
            'from_name' => env('MAIL_FROM_NAME', 'ThriftZone'),
        ],
    ],
];



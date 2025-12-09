<?php

use Bow\Payment\Payment;

return [
    /**
     * Define the model used my gateway
     */
    'model' => \App\Models\User::class,

    /**
     * The default gateway
     */
    'default' => [
        'gateway' => Payment::ORANGE,
        'country' => 'ci',
    ],

    /**
     * List of available gateway
     */
    'ivory_coast' => [
        'orange' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => ''
        ],

        'mtn' => [
            'subscription_key' => '',
            'api_user' => '',
            'api_key' => '',
            'environment' => 'sandbox', // or 'production'
            'webhook_secret' => ''
        ],

        'moov' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => ''
        ],

        'wave' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => ''
        ],

        'djamo' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => ''
        ]
    ],
];

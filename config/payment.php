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
    'ivoiry_cost' => [
        'orange' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => ''
        ],

        'mtn' => [
            'client_key' => '',
            'client_secret' => '',
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

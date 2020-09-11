<?php

return [
    /**
     * Define the model used my gateway
     */
    'model' => \App\Models\User::class,

    /**
     * The default gateway
     */
    'default' => 'orange_ci',

    /**
     * List of available gateway
     */
    'gateways' => [
        'orange_ci' => [
            'client_key' => '',
            'merchant_key' => ''
        ],

        'mtn_ci' => [
            'client_key' => '',
            'merchant_key' => ''
        ],

        'moov_ci' => [
            'client_key' => '',
            'merchant_key' => ''
        ]
    ]
];

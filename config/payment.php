<?php

use Bow\Payment\Processor;

return [
    /**
     * Define the model used my gateway
     */
    'model' => \App\Models\User::class,

    /**
     * The default gateway
     */
    'default' => [
        'gateway' => Processor::ORANGE,
        'country' => 'ivory_coast',
    ],

    /**
     * List of available gateway
     */
    'ivory_coast' => [
        'orange' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => '',
            'options' => [
                'notif_url' => '', // Notification URL
                'return_url' => '', // Return URL after payment
                'cancel_url' => '', // Cancel URL if payment failed
            ],
        ],

        'mtn' => [
            'api_user' => '',
            'api_key' => '',
            'webhook_secret' => '',
            'subscription_key' => '',
            'options' => [
                'notif_url' => '', // Notification URL
                'return_url' => '', // Return URL after payment
                'cancel_url' => '', // Cancel URL if payment failed
            ],
        ],

        'moov' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => '',
            'options' => [
                'merchant_id' => '',
                'notif_url' => '', // Notification URL
                'return_url' => '', // Return URL after payment
                'cancel_url' => '', // Cancel URL if payment failed
            ],
        ],

        'wave' => [
            'api_key' => '', // Your Wave API key (starts with wave_sn_prod_ or wave_sn_sandbox_)
            'webhook_secret' => '',
            'aggregated_merchant_id' => '',
            'options' => [
                'restrict_payer_mobile' => false,
                'aggregated_merchant_id' => '', // Aggregated Merchant ID for Senegal to override default
                'notif_url' => '', // Notification URL
                'success_url' => '', // Success URL after payment
                'error_url' => '', // Error URL if payment failed
            ],
        ],

        'djamo' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => '',
            'options' => [
                'notif_url' => '', // Notification URL
                'return_url' => '', // Return URL after payment
                'cancel_url' => '', // Cancel URL if payment failed
            ],
        ]
    ],

    /**
     * Senegal payment gateways configuration
     */
    'senegal' => [
        'orange' => [
            'client_key' => '',
            'client_secret' => '',
            'webhook_secret' => '',
            'options' => [
                'notif_url' => '', // Notification URL
                'return_url' => '', // Return URL after payment
                'cancel_url' => '', // Cancel URL if payment failed
            ],
        ],

        'wave' => [
            'api_key' => '', // Your Wave API key (starts with wave_sn_prod_ or wave_sn_sandbox_)
            'webhook_secret' => '',
            'aggregated_merchant_id' => '',
            'options' => [
                'restrict_payer_mobile' => false,
                'aggregated_merchant_id' => '', // Aggregated Merchant ID for Senegal to override default
                'notif_url' => '', // Notification URL
                'success_url' => '', // Success URL after payment
                'error_url' => '', // Error URL if payment failed
            ],
        ],
    ],
];

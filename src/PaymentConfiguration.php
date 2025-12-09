<?php

namespace Bow\Payment;

use Bow\Configuration\Configuration;
use Bow\Configuration\Loader as Config;

class PaymentConfiguration extends Configuration
{
    /**
     * Create payment configuration
     *
     * @param Config $config
     */
    public function create(Config $config): void
    {
        $payment = require __DIR__.'/../config/payment';

        $payment = array_merge($payment, $config['payment']);

        $config['payment'] = $payment;

        $this->container->make('payment', function ($config) {
            return Payment::configure($config['payment']);
        });
    }

    /**
     * Launch configuration
     *
     * @return mixed
     */
    public function run(): void
    {
        $this->container->make('payment');
    }
}

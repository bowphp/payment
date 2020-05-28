<?php

namespace Bow\Payment;

use Bow\Configuration\Configuration;
use Bow\Configuration\Loader as Config;

class PaymmentConfirguration extends Configuration
{
    /**
     * Create payment configuration
     *
     * @param Config $config
     */
    public function create(Config $config)
    {
        $payment = require __DIR__.'/../config/payment';

        $payment = array_merge($payment, $config['payment']);

        $config['payment'] = $payment;

        $this->container->make('payment', function ($config) {
            return Pay::configure($config['payment']);
        });
    }

    /**
     * Launch configuration
     *
     * @return mixed
     */
    public function run()
    {
        return $this->container->make('payment');
    }
}

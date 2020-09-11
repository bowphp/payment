<?php

namespace Bow\Payment;

use Bow\Payment\Common\PaymentManagerContract;
use BowcasherOrangeMoneyService;

class Bowcasher
{
    /**
     * Instance of
     *
     * @var PaymentManagerContract
     */
    private static $instance;

    /**
     * Make configuration
     *
     * @return PaymentManagerContract
     */
    public static function configure($config)
    {
        static::$instance = new BowcasherOrangeMoneyService($config['gateways']['orange_ci']);
    }

    /**
     * __callStatic
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (method_exists(static::$instance, $name)) {
            return call_user_func_array([static::$instance, $name], $arguments);
        }
    }
}

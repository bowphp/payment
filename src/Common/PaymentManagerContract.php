<?php

namespace Bow\Payment\Common;

abstract class PaymentManagerContract
{
    const PRODUCTION = 'production';
    const DEVELOPMENT = 'development';

    /**
     * Define the payment mode
     *
     * @var string
     */
    protected $environment = PaymentManagerContract::DEVELOPMENT;

    /**
     * Make pay
     * 
     * @return mixed
     */
    abstract public function pay(...$args);

    /**
     * Verify payment
     *
     * @return void
     */
    abstract public function verify();

    /**
     * Switch to production mode
     *
     * @return string
     */
    public function switchToProduction()
    {
        $this->environment = PaymentManagerContract::PRODUCTION;
    }

    /**
     * Switch to development mode
     *
     * @return string
     */
    public function switchToDevelopment()
    {
        $this->environment = PaymentManagerContract::DEVELOPMENT;
    }

    /**
     * Get the payment environment
     *
     * @param string $environment
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}

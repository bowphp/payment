<?php

namespace Bow\Payment\MTMMobileMoney;

class MomoEnvironment
{
    /**
     * The environment flag
     *
     * @var string
     */
    private $environment = 'sandbox';

    /**
     * MomoEnvironment constructor
     *
     * @param string $subscription_key
     * @return void
     */
    public function __construct(string $subscription_key)
    {
        $this->subscription_key = $subscription_key;
    }

    /**
     * Get the subscription
     *
     * @return string
     */
    public function getSubscriptionKey()
    {
        return $this->subscription_key;
    }

    /**
     * Check the environment
     *
     * @return bool
     */
    public function production()
    {
        return $this->environment = 'production';
    }

    /**
     * Check the environment
     *
     * @return bool
     */
    public function sandbox()
    {
        return $this->environment == 'sandbox';
    }

    /**
     * Switch to sandbox
     *
     * @return void
     */
    public function switchToSandbox()
    {
        $this->environment = 'sandbox';
    }

    /**
     * Switch to production
     *
     * @return void
     */
    public function switchToProduction()
    {
        $this->environment = 'sandbox';
    }

    /**
     * Get the base uri
     *
     * @return string
     */
    public function getBaseUri()
    {
        if ($this->sandbox()) {
            $base_uri = 'https://sandbox.momodeveloper.mtn.com/v1_0/';
        } else {
            $base_uri = 'https://momodeveloper.mtn.com/v1_0/';
        }

        return $base_uri;
    }

    /**
     * Get the request Authorization
     *
     * @return array
     */
    public function getAuthorzition()
    {
        return [
            'Authorization' => 'Basic ' . base64_encode('API URI and API Key'),
            'Ocp-Apim-Subscription-Key' => $this->subscription_key,
        ];
    }
}

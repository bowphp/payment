<?php

namespace Bow\Payment\MTMMobileMoney;

class MomoEnvironment
{
    /**
     * Define the environment flag
     *
     * @var string
     */
    private $environment = 'sandbox';

    /**
     * Define the basic auth value
     *
     * @var string
     */
    private $basic_auth;

    /**
     * MomoEnvironment constructor
     *
     * @param string $subscription_key
     * @return void
     */
    public function __construct(string $subscription_key. string $basic_auth)
    {
        $this->subscription_key = $subscription_key;
        $this->basic_auth = $basic_auth;
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
     * Get the basic authorization key
     *
     * @return string
     */
    public function getBasicAuthorizationKey()
    {
        return $this->basic_auth;
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
        $this->environment = 'production';
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
            'Authorization' => 'Basic ' . $this->basic_auth,
            'Ocp-Apim-Subscription-Key' => $this->subscription_key,
        ];
    }
}

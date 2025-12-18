<?php

namespace Bow\Payment\Gateway\IvoryCost\MTNMobileMoney;

class MomoEnvironment
{
    /**
     * Define the environment flag
     *
     * @var string
     */
    private $environment = 'sandbox';

    /**
     * Subscription key
     *
     * @var string
     */
    private $subscription_key;

    /**
     * API User ID
     *
     * @var string
     */
    private $api_user;

    /**
     * API Key
     *
     * @var string
     */
    private $api_key;

    /**
     * MomoEnvironment constructor
     *
     * @param string $subscription_key
     * @param string $api_user
     * @param string $api_key
     */
    public function __construct(
        string $subscription_key,
        string $api_user,
        string $api_key
    ) {
        $this->subscription_key = $subscription_key;
        $this->api_user = $api_user;
        $this->api_key = $api_key;
    }

    /**
     * Get the subscription key
     *
     * @return string
     */
    public function getSubscriptionKey(): string
    {
        return $this->subscription_key;
    }

    /**
     * Get the API user
     *
     * @return string
     */
    public function getApiUser(): string
    {
        return $this->api_user;
    }

    /**
     * Get the API key
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->api_key;
    }

    /**
     * Get authorization headers
     *
     * @return array
     */
    public function getAuthorization(): array
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->api_user . ':' . $this->api_key),
            'Ocp-Apim-Subscription-Key' => $this->subscription_key,
        ];
    }

    /**
     * Check if environment is production
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->environment === 'production';
    }

    /**
     * Check if environment is sandbox
     *
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->environment === 'sandbox';
    }

    /**
     * Switch to sandbox
     *
     * @return void
     */
    public function switchToSandbox(): void
    {
        $this->environment = 'sandbox';
    }

    /**
     * Switch to production
     *
     * @return void
     */
    public function switchToProduction(): void
    {
        $this->environment = 'production';
    }

    /**
     * Get the base URI
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->isSandbox()
            ? 'https://sandbox.momodeveloper.mtn.com'
            : 'https://momodeveloper.mtn.com';
    }
}

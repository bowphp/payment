<?php

namespace Bow\Payment\OrangeMoney;

use \GuzzleHttp\Client as HttpClient;

class OrangeMoneyEnvironment
{
    /**
     * Define the all request base url
     *
     * @var string
     */
    private $base_url = 'https://api.orange.com';

    /**
     * Define the token endpoint
     *
     * @var string
     */
    private $token_generator_endpoint = '/oauth/v2/token';

    /**
     * Define the transaction status endpoint
     *
     * @var string
     */
    private $transation_status_endpoint = '/orange-money-webpay/dev/v1/transactionstatus';

    /**
     * Define the payment endpoint
     *
     * @var string
     */
    private $payment_endpoint = '/orange-money-webpay/dev/v1/webpayment';

    /**
     * Set the transation status endpoint
     *
     * @param string $endpoint
     * @return void
     */
    public function setTransactionStatusEndpoint(string $endpoint)
    {
        $this->transation_status_endpoint = $endpoint;
    }

    /**
     * Set the token generator endpoint
     *
     * @param string $endpoint
     * @return void
     */
    public function setTokenGeneratorEndpoint(string $endpoint)
    {
        $this->token_generator_endpoint = $endpoint;
    }

    /**
     * Set the payment endpoint
     *
     * @param string $endpoint
     * @return void
     */
    public function setPaymentEndpoint(string $endpoint)
    {
        $this->payment_endpoint = $endpoint;
    }

    /**
     * Get the token generator
     *
     * @return string
     */
    public function getTokenGeneratorEndpoint()
    {
        return trim($this->base_url, '/') .'/'. trim($this->token_generator_endpoint, '/');
    }

    /**
     * Get the transaction status endpoint
     *
     * @return string
     */
    public function getTransactionStatusEndpoint()
    {
        return trim($this->base_url, '/') .'/'. trim($this->transation_status_endpoint, '/');
    }

    /**
     * Get the payment endpoint
     *
     * @return string
     */
    public function getPaymentEndpoint()
    {
        return trim($this->base_url, '/') .'/'. trim($this->payment_endpoint, '/');
    }
}

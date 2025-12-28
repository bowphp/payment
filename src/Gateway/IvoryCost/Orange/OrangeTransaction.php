<?php

namespace Bow\Payment\Gateway\IvoryCost\Orange;

use \GuzzleHttp\Client as HttpClient;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeToken;

class OrangeTransaction
{
    /**
     * The token generator response
     *
     * @var OrangeToken
     */
    private $token;

    /**
     * Define the HTTPClient base on Guzzle/http
     *
     * @var HttpClient
     */
    private $http;

    /**
     * Define the transaction dev url
     *
     * @var string
     */
    private $endpoint = '/orange-money-webpay/dev/v1/transactionstatus';

    /**
     * OrangeTransactionStatus constructor
     *
     * @param OrangeToken $token
     * @return mixed
     */
    public function __construct(OrangeToken $token)
    {
        $this->token = $token;

        $this->http = new HttpClient(['base_uri' => 'https://api.orange.com']);
    }

    /**
     * Set transaction status
     *
     * @param string $endpoint
     * @return mixed
     */
    public function setTransactionStatusUrl($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Check the payment status
     *
     * @param int|double $amount
     * @param string $order_id
     * @param string $pay_token
     */
    public function check($amount, string $order_id, string $pay_token)
    {
        $response = $this->http->post($this->endpoint, [
            "json" => compact('order_id', 'amount', 'pay_token'),
            'headers' => [
                'Authorization' => (string) $this->token,
                'Content-Type' => "application/json",
                "Accept" => "application/json"
            ]
        ]);

        // Cast the request response
        $status = json_decode($response->getBody()->getContents());

        return new OrangeTransactionStatus(
            $status->status,
            $status->notif_token
        );
    }

    /**
     * Check the payment have failed
     *
     * @param string $order_id
     * @param int|double $amount
     * @param string $pay_token
     */
    public function checkIfHasFail($amount, string $order_id, string $pay_token)
    {
        $status = $this->check($amount, $order_id, $pay_token);

        return $status->isFailed();
    }

    /**
     * Check the payment have pending
     *
     * @param string $order_id
     * @param int|double $amount
     * @param string $pay_token
     */
    public function checkIfHasPending($amount, string $order_id, string $pay_token)
    {
        $status = $this->check($amount, $order_id, $pay_token);

        return $status->isPending();
    }

    /**
     * Check the payment have success
     *
     * @param string $order_id
     * @param int|double $amount
     * @param string $pay_token
     */
    public function checkIfHasSuccess($amount, string $order_id, string $pay_token)
    {
        $status = $this->check($amount, $order_id, $pay_token);

        return $status->isSuccess();
    }

    /**
     * Set the transaction status
     *
     * @param string $endpoint
     */
    public function setTransactionStatusEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }
}

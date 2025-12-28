<?php

namespace Bow\Payment\Gateway\IvoryCost\Orange;

use \GuzzleHttp\Client as HttpClient;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeToken;

class OrangePayment
{
    /**
     * HTTP client instance
     *
     * @var HttpClient
     */
    private $http;
    
    /**
     * The pay route
     *
     * @var string
     */
    private $pay_url = '/orange-money-webpay/dev/v1/webpayment';
    
    /**
     * The return url
     *
     * @var string
     */
    private $return_url;
    
    /**
     * The cancel payment url redirection
     *
     * @var string
     */
    private $cancel_url;
    
    /**
     * The notify url for successful payment
     *
     * @var string
     */
    private $notif_url;
    
    /**
     * Orange constructor
     *
     * @param OrangeToken $token
     * @param string $merchant_key
     * @param string $currency
     * @return mixed
     */
    public function __construct(private OrangeToken $token, private string $merchant_key, private string $currency = 'OUV')
    {
        $this->http = new HttpClient(['base_uri' => 'https://api.orange.com']);
    }

    /**
     * Make payment
     *
     * @param int|double $amount
     * @param string $reference
     * @return Orange
     */
    public function prepare($amount, string $reference)
    {
        $response = $this->http->post($this->pay_url, [
            'json' => $this->buildRequestData($amount, $reference),
            'headers' => [
                'Authorization' => (string) $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        // Parse Json data
        $data = json_decode($response->getBody()->getContents(), true);

        return new OrangeResponse(
            $data['payment_url'],
            $data['pay_token'],
            $data['notif_token']
        );
    }

    /**
     * Set the return url when the payment have successful
     *
     * @param string $url
     */
    public function setReturnUrl(string $url)
    {
        $this->return_url = $url;
    }
    
    /**
     * Set the notify payment url when it's successful
     *
     * @param string $url
     */
    public function setNotifyUrl(string $url)
    {
        $this->notif_url = $url;
    }
    
    /**
     * Set the cancel payment url redirection
     *
     * @param string $url
     */
    public function setCancelUrl(string $url)
    {
        $this->cancel_url = $url;
    }
    
    /**
     * Set the payment currency
     *
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * Set the payment route
     *
     * @deprecated
     * @param string $url
     */
    public function setPaymentUrl($url)
    {
        $this->pay_url = $url;
    }

    /**
     * Set the payment route
     *
     * @param string $url
     */
    public function setPaymentEndpoint($url)
    {
        $this->pay_url = $url;
    }

    /**
     * Set the merchant id
     *
     * @param string $merchant_key
     * @return mixed
     */
    public function setMerchantId(string $merchant_key)
    {
        $this->merchant_key = $merchant_key;
    }

    /**
     * Build the request data
     *
     * @param int|double $amount
     * @param string $reference
     * @return array
     */
    protected function buildRequestData($amount, string $reference)
    {
        return [
            "merchant_key" => $this->merchant_key,
            "currency" => $this->currency,
            "amount" => $amount,
            'order_id' => $reference,
            "return_url" => $this->return_url,
            "cancel_url" => $this->cancel_url,
            "notif_url" => $this->notif_url,
            "lang" => "fr",
            "reference" => $reference
        ];
    }
}

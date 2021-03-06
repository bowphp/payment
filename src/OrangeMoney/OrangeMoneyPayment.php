<?php

namespace Bow\Payment\OrangeMoney;

use Bow\Payment\Common\PaymentToken as OrangeMoneyToken;
use \GuzzleHttp\Client as HttpClient;

class OrangeMoneyPayment
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
     * The generate orange money token
     *
     * @var OrangeMoneyToken
     */
    private $token;
    
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
     * The merchant id
     *
     * @var string
     */
    private $merchant_key;
    
    /**
     * OrangeMoney constructor
     *
     * @param OrangeMoneyToken $token
     * @param string $merchant_key
     * @param string $currency
     * @return mixed
     */
    public function __construct(OrangeMoneyToken $token, $merchant_key, string $currency = 'OUV')
    {
        $this->token = $token;
        
        $this->http = new HttpClient(['base_uri' => 'https://api.orange.com']);
        
        $this->merchant_key = $merchant_key;
        
        $this->currency = $currency;
    }

    /**
     * Make payment
     *
     * @param int|double $amount
     * @param string $order_id
     * @param string $reference
     * @return OrangeMoney
     */
    public function prepare($amount, string $order_id, string $reference)
    {
        $response = $this->http->post($this->pay_url, [
            'json' => $this->buildRequestData($amount, $reference, $order_id),
            'headers' => [
                'Authorization' => (string) $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        // Parse Json data
        $data = json_decode($response->getBody()->getContents(), true);

        return new OrangeMoney(
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
     * @param string $order_id
     * @return array
     */
    protected function buildRequestData($amount, string $reference, string $order_id)
    {
        return [
            "merchant_key" => $this->merchant_key,
            "currency" => $this->currency,
            "order_id" => $order_id,
            "amount" => $amount,
            "return_url" => $this->return_url,
            "cancel_url" => $this->cancel_url,
            "notif_url" => $this->notif_url,
            "lang" => "fr",
            "reference" => $reference
        ];
    }
}

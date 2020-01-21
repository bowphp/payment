<?php

namespace Bow\Payment\OrangeMoney;

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
     * The merchand id
     *
     * @var string
     */
    private $merchant_key;
    
    /**
     * OrangeMoney contructor
     *
     * @param OrangeMoneyToken $token
     * @param string $merchant_key
     * @return mixed
     */
    public function __construct(OrangeMoneyToken $token, $merchant_key)
    {
        $this->token = $token;
        
        $this->http = new HttpClient(['base_uri' => 'https://api.orange.com']);
        
        $this->merchant_key = $merchant_key;
        
        $this->currency = 'OUV';
    }
    
    /**
     * Make payment
     *
     * @param float $amount
     * @param mixed $order_id
     * @param mixed $reference
     * @return OrangeMoney
     */
    public function prepare($amount, $order_id, $reference)
    {
        $response = $this->http->post('http://localhost:5000/server.php', [
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
    public function setReturnUrl($url)
    {
        $this->return_url = $url;
    }
    
    /**
     * Set the notify payment url when it's successful
     *
     * @param string $url
     */
    public function setNotifyUrl($url)
    {
        $this->notif_url = $url;
    }
    
    /**
     * Set the cancel payment url redirection
     *
     * @param string $url
     */
    public function setCancelUrl($url)
    {
        $this->cancel_url = $url;
    }
    
    /**
     * Set the payment currency
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Set the merchand id
     *
     * @param string $merchant_key
     * @return mixed
     */
    public function setMerchandId($merchant_key)
    {
        $this->merchant_key = $merchant_key;
    }

    /**
     * Build the request data
     *
     * @param float $amount
     * @param string $reference
     * @param mixed $order_id
     * @return array
     */
    protected function buildRequestData($amount, $reference, $order_id)
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

<?php

namespace Bow\Payment\OrangeMoney;

use \GuzzleHttp\Client as HttpClient;

class OrangeMoney
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
    private $pay_url = 'https://api.orange.com/orange-money-webpay/dev/v1/webpayment';

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
    private $merchant_id;

    /**
	 * OrangeMoney contructor
	 * 
	 * @return mixed
	 */
	public function __construct(OrangeMoneyToken $token, $merchant_id)
	{
        $this->token = $token;

        $this->http = new HttpClient;

        $this->merchant_id = $merchant_id;

        $this->currency = 'OUV';
    }

    /**
     * Make payment
     * 
     * @return 
     */
    public function pay($amount, $reference, $order_id)
    {
        $response = $this->http->post($this->pay_url, [
            'form_params' => [
                "merchant_key" => $this->merchant_id,
                "currency" => $this->currency,
                "order_id" => $order_id,
                "amount" => $amount,
                "return_url" => $this->return_url,
                "cancel_url" => $this->cancel_url,
                "notif_url" => $this->notif_url,
                "lang" => "fr",
                "reference" => $reference
            ],
            "headers" => [
                "Authorization" => $this->token
            ]
        ]);
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
}
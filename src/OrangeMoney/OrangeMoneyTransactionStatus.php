<?php

namespace Bow\Payment\OrangeMoney;

use \GuzzleHttp\Client as HttpClient;

class OrangeMoneyTransactionStatus
{
    /**
     * The token generator response
     *
     * @var OrangeMoneyToken
     */
    private $token;

    /**
     * OrangeMoneyTransactionStatus constructor
     *
     * @param OrangeMoneyTransactionStatus $token
     * @return mixed
     */
    public function __construct(OrangeMoneyToken $token)
    {
        $this->token = $token;
    }

    /**
     * Check the payment status
     *
     * @param string $order_id
     * @param int|float $amount
     * @param string $pay_token
     */
    public function check($order_id, $amount, $pay_token)
    {
        $response = (new HttpClient)->post('https://api.orange.com/orange-money-webpay/dev/v1/transactionstatus', [
            "json" => compact('order_id', 'amount', 'pay_token'),
            'headers' => [
                'Authorization' => (string) $this->token,
                'Content-Type' => "application/json",
                "Accept" => "application/json"
            ]
        ]);
            
        // Cast the request response
        return json_decode($response->getBody()->getContents());
    }
}

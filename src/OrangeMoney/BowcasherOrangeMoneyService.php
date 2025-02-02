<?php

use Bow\Payment\Common\PaymentManagerContract;
use Bow\Payment\Common\TransactionStatusContract;
use Bow\Payment\OrangeMoney\OrangeMoneyPayment;
use Bow\Payment\OrangeMoney\OrangeMoneyTokenGenerator;
use Bow\Payment\OrangeMoney\OrangeMoneyTransaction;

class BowcasherOrangeMoneyService extends PaymentManagerContract
{
    /**
     * Define the configuration
     *
     * @var string
     */
    private $config;
    
    /**
     * BowcasherForOrangeMoney constructor
     *
     * @param array $config
     * @return mixed
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
    * Make payment
    *
    * @return void
    */
    public function pay(...$args)
    {
        $token_generator = $this->getTokenGenerator();

        $payment = new OrangeMoneyPayment(
            $token_generator->getToken(), 
            $this->config['merchant_key']
        );
        
        // Set the right production endpoint
        if ($this->getEnvironment() == PaymentManagerContract::PRODUCTION) {
            $payment->setPaymentEndpoint('/orange-money-webpay/v1/webpayment');
        }

        $payment->setNotifyUrl($args['notif_url']);
        $payment->setCancelUrl($args['cancel_url']);
        $payment->setReturnUrl($args['return_url']);

        $amount = $args['amount'];
        $order_id = $args['order_id'];
        $reference = $args['reference'];

        $orange = $payment->prepare($amount, $order_id, $reference);
        $payment_information = $orange->getPaymentInformation();

        // Redirect to payment plateforme
        $orange->pay();
    }

    /**
     * Verify payment
     *
     * @param array ...$args
     * @return TransactionStatusContract
     */
    public function verify(...$args)
    {
        $token_generator = $this->getTokenGenerator();
        // Transaction status
        $transaction = new OrangeMoneyTransaction($token_generator->getToken());

        // Set the production url
        if ($this->getEnvironment() == PaymentManagerContract::PRODUCTION) {
            $transaction->setTransactionStatusEndpoint('/orange-money-webpay/v1/transactionstatus');
        }

        $amount = $args['amount'];
        $order_id = $args['order_id'];
        $reference = $args['reference'];

        // Check the transaction status
        return $transaction->check($amount, $order_id, $reference);
    }

    /**
     * Create the Token Generator instance
     *
     * @return OrangeMoneyTokenGenerator
     */
    private function getTokenGenerator()
    {
        $token_generator = new OrangeMoneyTokenGenerator($this->config['client_key']);

        // Set the right production endpoint
        if ($this->getEnvironment() == PaymentManagerContract::PRODUCTION) {
            $token_generator->setTokenGeneratorEndpoint('/oauth/v2/token');
        }

        return $token_generator;
    }
}
    
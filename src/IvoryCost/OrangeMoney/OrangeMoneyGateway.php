<?php

namespace Bow\Payment\IvoryCost\OrangeMoney;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Common\ProcessorTransactionStatusInterface;
use Bow\Payment\Exceptions\PaymentRequestException;
use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyPayment;
use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyTokenGenerator;
use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyTransaction;

class OrangeMoneyGateway implements ProcessorGatewayInterface
{
    /**
     * ForOrangeMoney constructor
     *
     * @param array $config
     */
    public function __construct(private array $config)
    {
    }

    /**
    * Make payment
    *
    * @return void
    */
    public function payment(...$args)
    {
        $token_generator = $this->getTokenGenerator();

        $payment = new OrangeMoneyPayment(
            $token_generator->getToken(), 
            $this->config['client_secret'],
        );
        
        // Set the right production endpoint
        $payment->setPaymentEndpoint('/orange-money-webpay/v1/webpayment');

        if (isset($args['notif_url'])) {
            $payment->setNotifyUrl($args['notif_url']);
        }

        if (isset($args['cancel_url'])) {
            $payment->setCancelUrl($args['cancel_url']);
        }

        if (isset($args['return_url'])) {
            $payment->setReturnUrl($args['return_url']);
        }

        $amount = $args['amount'];
        $reference = $args['reference'];

        $orange = $payment->prepare($amount, $reference);

        $payment_information = $orange->getPaymentInformation();

        // Redirect to payment plateforme
        $orange->pay();
    }

    /**
     * Verify payment
     *
     * @param array ...$args
     * @return ProcessorStatusInterface
     */
    public function verify(...$args)
    {
        $token_generator = $this->getTokenGenerator();

        // Transaction status
        $transaction = new OrangeMoneyTransaction($token_generator->getToken());

        // Set the production url
        $transaction->setTransactionStatusEndpoint('/orange-money-webpay/v1/transactionstatus');

        $amount = $args['amount'];
        $order_id = $args['order_id'];
        $pay_token = $args['pay_token'];

        // Check the transaction status
        return $transaction->check($amount, $order_id, $pay_token);
    }

    /**
     * Transfer money
     *
     * @param array ...$args
     * @return mixed
     */
    public function transfer(...$args)
    {
        throw new PaymentRequestException(
            'Orange Money payment gateway is not yet implemented. Implementation pending official API documentation.'
        );
    }

    /**
     * Get balance
     *
     * @param array ...$args
     * @return mixed
     */
    public function balance(...$args)
    {
        throw new PaymentRequestException(
            'Orange Money balance inquiry is not yet implemented.'
        );
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
        $token_generator->setTokenGeneratorEndpoint('/oauth/v2/token');

        return $token_generator;
    }
}

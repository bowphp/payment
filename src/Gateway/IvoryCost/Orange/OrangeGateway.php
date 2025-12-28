<?php

namespace Bow\Payment\Gateway\IvoryCost\Orange;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Exceptions\PaymentRequestException;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangePayment;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeTokenGenerator;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeTransaction;

class OrangeGateway implements ProcessorGatewayInterface
{
    /**
     * ForOrange constructor
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
    public function payment(array $params)
    {
        $token_generator = $this->getTokenGenerator();

        $payment = new OrangePayment(
            $token_generator->getToken(), 
            $this->config['client_secret'],
        );
        
        // Set the right production endpoint
        $payment->setPaymentEndpoint('/orange-money-webpay/v1/webpayment');

        if (isset($params['notif_url'])) {
            $payment->setNotifyUrl($params['notif_url']);
        }

        if (isset($params['cancel_url'])) {
            $payment->setCancelUrl($params['cancel_url']);
        }

        if (isset($params['return_url'])) {
            $payment->setReturnUrl($params['return_url']);
        }

        $amount = $params['amount'];
        $reference = $params['reference'];

        $orange = $payment->prepare($amount, $reference);

        $payment_information = $orange->getPaymentInformation();

        // Redirect to payment plateforme
        $orange->pay();
    }

    /**
     * Verify payment
     *
     * @param array $params
     * @return ProcessorStatusInterface
     */
    public function verify(array $params)
    {
        $token_generator = $this->getTokenGenerator();

        // Transaction status
        $transaction = new OrangeTransaction($token_generator->getToken());

        // Set the production url
        $transaction->setTransactionStatusEndpoint('/orange-money-webpay/v1/transactionstatus');

        $amount = $params['amount'];
        $order_id = $params['order_id'];
        $pay_token = $params['pay_token'];

        // Check the transaction status
        return $transaction->check($amount, $order_id, $pay_token);
    }

    /**
     * Transfer money
     *
     * @param array $params
     * @return mixed
     */
    public function transfer(array $params)
    {
        throw new PaymentRequestException(
            'Orange Money payment gateway is not yet implemented. Implementation pending official API documentation.'
        );
    }

    /**
     * Get balance
     *
     * @param array $params
     * @return mixed
     */
    public function balance(array $params = [])
    {
        throw new PaymentRequestException(
            'Orange Money balance inquiry is not yet implemented.'
        );
    }

    /**
     * Validate payment data
     *
     * @param array $params
     * @return void
     */
    public function validatePaymentData(array $params): void
    {
        // Validation logic can be implemented here as needed
    }

    /**
     * Create the Token Generator instance
     *
     * @return OrangeTokenGenerator
     */
    private function getTokenGenerator()
    {
        $token_generator = new OrangeTokenGenerator($this->config['client_key']);

        // Set the right production endpoint
        $token_generator->setTokenGeneratorEndpoint('/oauth/v2/token');

        return $token_generator;
    }
}

<?php

namespace Bow\Payment\Gateway\IvoryCost\Mono;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Gateway\IvoryCost\Mono\Collection\MomoPayment;
use Bow\Payment\Gateway\IvoryCost\Mono\Collection\MomoTransaction;
use Bow\Payment\Gateway\IvoryCost\Mono\MomoTokenGenerator;
use Bow\Payment\Gateway\IvoryCost\Mono\MomoEnvironment;

class MonoGateway implements ProcessorGatewayInterface
{
    /**
     * Environment instance
     *
     * @var MomoEnvironment
     */
    private $environment;

    /**
     * Token generator instance
     *
     * @var MomoTokenGenerator
     */
    private $tokenGenerator;

    /**
     * MTN Mobile Money Gateway constructor
     *
     * @param array $config
     */
    public function __construct(private array $config)
    {
        $this->environment = new MomoEnvironment(
            $config['subscription_key'] ?? '',
            $config['api_user'] ?? '',
            $config['api_key'] ?? ''
        );

        // Set environment
        if (isset($config['environment']) && $config['environment'] === 'production') {
            $this->environment->switchToProduction();
        }

        $this->tokenGenerator = new MomoTokenGenerator($this->environment);
    }

    /**
     * Make payment
     *
     * @param array $params
     * @return mixed
     */
    public function payment(array $params)
    {
        $token = $this->tokenGenerator->getToken();

        $payment = new MomoPayment($token, $this->environment);

        $this->validatePaymentData($params);

        $amount = $params['amount'];
        $phone = $params['phone_number'];
        $reference = $params['reference'] ?? uniqid('momo_');
        $currency = $params['currency'] ?? 'XOF';

        return $payment->requestToPay([
            'amount' => $amount,
            'phone' => $phone,
            'reference' => $reference,
            'currency' => $currency,
            'payer_message' => $args['payer_message'] ?? 'Payment',
            'payee_note' => $args['payee_note'] ?? 'Payment received',
        ]);
    }

    /**
     * Make transfer
     *
     * @param array $params
     * @return mixed
     */
    public function transfer(array $params)
    {
        // MTN Mobile Money CI uses Collection API for payments
        // Transfer functionality would require Disbursement API
        throw new \BadMethodCallException('Transfer not yet implemented for MTN Mobile Money');
    }

    /**
     * Get balance
     *
     * @param array $params
     * @return mixed
     */
    public function balance(array $params = [])
    {
        $token = $this->tokenGenerator->getToken();
        $transaction = new MomoTransaction($token, $this->environment);

        return $transaction->getAccountBalance();
    }

    /**
     * Verify payment
     *
     * @return mixed
     */
    public function verify(array $params)
    {
        $token = $this->tokenGenerator->getToken();

        $transaction = new MomoTransaction($token, $this->environment);

        $referenceId = $params['reference'];

        return $transaction->getTransactionStatus($referenceId);
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
}

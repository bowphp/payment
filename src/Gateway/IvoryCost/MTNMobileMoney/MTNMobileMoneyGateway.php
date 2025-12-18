<?php

namespace Bow\Payment\Gateway\IvoryCost\MTNMobileMoney;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\Collection\MomoPayment;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\Collection\MomoTransaction;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\MomoTokenGenerator;
use Bow\Payment\Gateway\IvoryCost\MTNMobileMoney\MomoEnvironment;

class MTNMobileMoneyGateway implements ProcessorGatewayInterface
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
     * @param mixed ...$args
     * @return mixed
     */
    public function payment(...$args)
    {
        $token = $this->tokenGenerator->getToken();

        $payment = new MomoPayment($token, $this->environment);

        $amount = $args['amount'] ?? $args[0];
        $phone = $args['phone'] ?? $args[1];
        $reference = $args['reference'] ?? $args[2] ?? uniqid('momo_');
        $currency = $args['currency'] ?? 'XOF';

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
     * @param mixed ...$args
     * @return mixed
     */
    public function transfer(...$args)
    {
        // MTN Mobile Money CI uses Collection API for payments
        // Transfer functionality would require Disbursement API
        throw new \BadMethodCallException('Transfer not yet implemented for MTN Mobile Money');
    }

    /**
     * Get balance
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function balance(...$args)
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
    public function verify(...$args)
    {
        $token = $this->tokenGenerator->getToken();
        $transaction = new MomoTransaction($token, $this->environment);

        $referenceId = $args['reference_id'] ?? $args[0];

        return $transaction->getTransactionStatus($referenceId);
    }
}

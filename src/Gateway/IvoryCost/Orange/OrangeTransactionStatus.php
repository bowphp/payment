<?php

namespace Bow\Payment\Gateway\IvoryCost\Orange;

use Bow\Payment\Common\PaymentStatus;
use Bow\Payment\Common\ProcessorStatusInterface;

class OrangeTransactionStatus implements ProcessorStatusInterface
{
    /**
     * Define the transaction status
     *
     * @var string
     */
    private $status;

    /**
     * OrangeTransactionStatus constructor
     *
     * @param string $status
     * @return void
     */
    public function __construct(string $status)
    {
        $this->status = strtoupper($status);
    }

    /**
     * Define if transaction fail
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status == 'FAIL';
    }

    /**
     * Define if transaction initiated
     *
     * @return bool
     */
    public function isInitiated()
    {
        return $this->status == 'INITIATED';
    }

    /**
     * Define if transaction expired
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->status == 'EXPIRED';
    }

    /**
     * Define if transaction success
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status == 'SUCCESS';
    }

    /**
     * Define if transaction pending
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status == 'PENDING';
    }

    /**
     * Get payment status string
     *
     * @return string
     */
    public function getStatus(): string
    {
        if ($this->isSuccess()) {
            return PaymentStatus::COMPLETED;
        }

        if ($this->isPending()) {
            return PaymentStatus::PENDING;
        }

        if ($this->isFailed()) {
            return PaymentStatus::FAILED;
        }

        return PaymentStatus::UNKNOWN;
    }
}

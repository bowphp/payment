<?php

namespace Bow\Payment\Gateway\IvoryCost\Mono\Collection;

use Bow\Payment\Common\ProcessorStatusInterface;

class MomoPaymentStatus implements ProcessorStatusInterface
{
    /**
     * Transaction status
     *
     * @var string
     */
    private $status;

    /**
     * Transaction data
     *
     * @var array
     */
    private $data;

    /**
     * MomoPaymentStatus constructor
     *
     * @param string $status
     * @param array $data
     */
    public function __construct(string $status, array $data = [])
    {
        $this->status = $status;
        $this->data = $data;
    }

    /**
     * Check if transaction failed
     *
     * @return bool
     */
    public function isFail(): bool
    {
        return in_array($this->status, ['FAILED', 'REJECTED']);
    }

    /**
     * Check if transaction was initiated
     *
     * @return bool
     */
    public function isInitiated(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if transaction expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->status === 'EXPIRED';
    }

    /**
     * Check if transaction succeeded
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->status === 'SUCCESSFUL';
    }

    /**
     * Check if transaction is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Get the transaction status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get transaction data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}


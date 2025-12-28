<?php

namespace Bow\Payment\Gateway\IvoryCost\Wave;

use Bow\Payment\Common\PaymentStatus;
use Bow\Payment\Common\ProcessorStatusInterface;
use Bow\Payment\Payment;

/**
 * Wave Payment Status
 * Wraps WaveCheckoutSession to provide status information
 */
class WavePaymentStatus implements ProcessorStatusInterface
{
    /**
     * Checkout session
     *
     * @var WaveCheckoutSession
     */
    private WaveCheckoutSession $session;

    /**
     * WavePaymentStatus constructor
     *
     * @param WaveCheckoutSession $session
     */
    public function __construct(WaveCheckoutSession $session)
    {
        $this->session = $session;
    }

    /**
     * Check if payment is successful
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->session->isComplete() && $this->session->isPaymentSucceeded();
    }

    /**
     * Check if payment is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->session->isOpen() || $this->session->isPaymentProcessing();
    }

    /**
     * Check if payment failed
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->session->isPaymentCancelled() || 
               ($this->session->isExpired() && !$this->session->isPaymentSucceeded());
    }

    /**
     * Check if payment failed (alias for isFailed)
     *
     * @return bool
     */
    public function isFaileded(): bool
    {
        return $this->isFailed();
    }

    /**
     * Check if transaction is initiated
     *
     * @return bool
     */
    public function isInitiated(): bool
    {
        return $this->session->isOpen();
    }

    /**
     * Check if transaction is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->session->isExpired();
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

    /**
     * Get transaction ID
     *
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->session->getTransactionId();
    }

    /**
     * Get checkout session ID
     *
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->session->getId();
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount(): string
    {
        return $this->session->getAmount();
    }

    /**
     * Get checkout status
     *
     * @return string
     */
    public function getCheckoutStatus(): string
    {
        return $this->session->getCheckoutStatus();
    }

    /**
     * Get payment status
     *
     * @return string
     */
    public function getPaymentStatus(): string
    {
        return $this->session->getPaymentStatus();
    }

    /**
     * Get client reference
     *
     * @return string|null
     */
    public function getClientReference(): ?string
    {
        return $this->session->getClientReference();
    }

    /**
     * Get error details if payment failed
     *
     * @return array|null
     */
    public function getError(): ?array
    {
        return $this->session->getLastPaymentError();
    }

    /**
     * Get error message
     *
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        $error = $this->session->getLastPaymentError();
        return $error['message'] ?? null;
    }

    /**
     * Get error code
     *
     * @return string|null
     */
    public function getErrorCode(): ?string
    {
        $error = $this->session->getLastPaymentError();
        return $error['code'] ?? null;
    }

    /**
     * Get the underlying checkout session
     *
     * @return WaveCheckoutSession
     */
    public function getSession(): WaveCheckoutSession
    {
        return $this->session;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->getStatus(),
            'is_success' => $this->isSuccess(),
            'is_pending' => $this->isPending(),
            'is_failed' => $this->isFailed(),
            'is_expired' => $this->isExpired(),
            'is_initiated' => $this->isInitiated(),
            'session_id' => $this->getSessionId(),
            'transaction_id' => $this->getTransactionId(),
            'amount' => $this->getAmount(),
            'checkout_status' => $this->getCheckoutStatus(),
            'payment_status' => $this->getPaymentStatus(),
            'client_reference' => $this->getClientReference(),
            'error' => $this->getError(),
            'session' => $this->session->toArray(),
        ];
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->toArray());
    }
}

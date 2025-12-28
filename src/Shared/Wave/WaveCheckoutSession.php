<?php

namespace Bow\Payment\Shared\Wave;

/**
 * Wave Checkout Session
 * Represents a Wave checkout session object
 */
class WaveCheckoutSession
{
    /**
     * WaveCheckoutSession constructor
     *
     * @param string $id
     * @param string $amount
     * @param string $checkoutStatus
     * @param string $paymentStatus
     * @param string $currency
     * @param string $businessName
     * @param string $successUrl
     * @param string $errorUrl
     * @param string $waveLaunchUrl
     * @param string|null $transactionId
     * @param string|null $clientReference
     * @param string|null $aggregatedMerchantId
     * @param string|null $restrictPayerMobile
     * @param array|null $lastPaymentError
     * @param string $whenCreated
     * @param string|null $whenCompleted
     * @param string $whenExpires
     */
    public function __construct(
        private string $id,
        private string $amount,
        private string $checkoutStatus,
        private string $paymentStatus,
        private string $currency,
        private string $businessName,
        private string $successUrl,
        private string $errorUrl,
        private string $waveLaunchUrl,
        private ?string $transactionId = null,
        private ?string $clientReference = null,
        private ?string $aggregatedMerchantId = null,
        private ?string $restrictPayerMobile = null,
        private ?array $lastPaymentError = null,
        private string $whenCreated = '',
        private ?string $whenCompleted = null,
        private string $whenExpires = ''
    ) {
    }

    /**
     * Create from API response
     *
     * @param array $data
     * @return self
     */
    public static function fromResponse(array $data): self
    {
        return new self(
            id: $data['id'],
            amount: $data['amount'],
            checkoutStatus: $data['checkout_status'],
            paymentStatus: $data['payment_status'],
            currency: $data['currency'],
            businessName: $data['business_name'],
            successUrl: $data['success_url'],
            errorUrl: $data['error_url'],
            waveLaunchUrl: $data['wave_launch_url'],
            transactionId: $data['transaction_id'] ?? null,
            clientReference: $data['client_reference'] ?? null,
            aggregatedMerchantId: $data['aggregated_merchant_id'] ?? null,
            restrictPayerMobile: $data['restrict_payer_mobile'] ?? null,
            lastPaymentError: $data['last_payment_error'] ?? null,
            whenCreated: $data['when_created'],
            whenCompleted: $data['when_completed'] ?? null,
            whenExpires: $data['when_expires']
        );
    }

    /**
     * Get checkout session ID
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Get checkout status
     *
     * @return string
     */
    public function getCheckoutStatus(): string
    {
        return $this->checkoutStatus;
    }

    /**
     * Get payment status
     *
     * @return string
     */
    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    /**
     * Get Wave launch URL
     *
     * @return string
     */
    public function getWaveLaunchUrl(): string
    {
        return $this->waveLaunchUrl;
    }

    /**
     * Get transaction ID
     *
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    /**
     * Get client reference
     *
     * @return string|null
     */
    public function getClientReference(): ?string
    {
        return $this->clientReference;
    }

    /**
     * Check if checkout is open
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return $this->checkoutStatus === 'open';
    }

    /**
     * Check if checkout is complete
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->checkoutStatus === 'complete';
    }

    /**
     * Check if checkout is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->checkoutStatus === 'expired';
    }

    /**
     * Check if payment succeeded
     *
     * @return bool
     */
    public function isPaymentSucceeded(): bool
    {
        return $this->paymentStatus === 'succeeded';
    }

    /**
     * Check if payment is processing
     *
     * @return bool
     */
    public function isPaymentProcessing(): bool
    {
        return $this->paymentStatus === 'processing';
    }

    /**
     * Check if payment is cancelled
     *
     * @return bool
     */
    public function isPaymentCancelled(): bool
    {
        return $this->paymentStatus === 'cancelled';
    }

    /**
     * Get last payment error
     *
     * @return array|null
     */
    public function getLastPaymentError(): ?array
    {
        return $this->lastPaymentError;
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'checkout_status' => $this->checkoutStatus,
            'payment_status' => $this->paymentStatus,
            'currency' => $this->currency,
            'business_name' => $this->businessName,
            'success_url' => $this->successUrl,
            'error_url' => $this->errorUrl,
            'wave_launch_url' => $this->waveLaunchUrl,
            'transaction_id' => $this->transactionId,
            'client_reference' => $this->clientReference,
            'aggregated_merchant_id' => $this->aggregatedMerchantId,
            'restrict_payer_mobile' => $this->restrictPayerMobile,
            'last_payment_error' => $this->lastPaymentError,
            'when_created' => $this->whenCreated,
            'when_completed' => $this->whenCompleted,
            'when_expires' => $this->whenExpires,
        ];
    }
}

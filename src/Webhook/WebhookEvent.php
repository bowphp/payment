<?php

namespace Bow\Payment\Webhook;

/**
 * Webhook event data class
 */
class WebhookEvent
{
    /**
     * Event types
     */
    public const PAYMENT_SUCCESS = 'payment.success';
    public const PAYMENT_FAILED = 'payment.failed';
    public const PAYMENT_PENDING = 'payment.pending';
    public const PAYMENT_EXPIRED = 'payment.expired';
    public const TRANSFER_SUCCESS = 'transfer.success';
    public const TRANSFER_FAILED = 'transfer.failed';

    /**
     * Provider name
     *
     * @var string
     */
    private $provider;

    /**
     * Event payload
     *
     * @var array
     */
    private $payload;

    /**
     * Create a new webhook event
     *
     * @param string $provider
     * @param array $payload
     */
    public function __construct(string $provider, array $payload)
    {
        $this->provider = $provider;
        $this->payload = $payload;
    }

    /**
     * Get the provider name
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Get the event payload
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * Get event type
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->payload['event'] ?? $this->payload['type'] ?? null;
    }

    /**
     * Get transaction ID
     *
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->payload['transaction_id'] 
            ?? $this->payload['reference'] 
            ?? $this->payload['id'] 
            ?? null;
    }

    /**
     * Get transaction status
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->payload['status'] ?? null;
    }

    /**
     * Get transaction amount
     *
     * @return float|null
     */
    public function getAmount(): ?float
    {
        $amount = $this->payload['amount'] ?? null;
        return $amount ? (float) $amount : null;
    }

    /**
     * Get currency
     *
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->payload['currency'] ?? null;
    }

    /**
     * Check if event is a payment success
     *
     * @return bool
     */
    public function isPaymentSuccess(): bool
    {
        $status = strtolower($this->getStatus() ?? '');
        return in_array($status, ['success', 'successful', 'completed', 'paid']);
    }

    /**
     * Check if event is a payment failure
     *
     * @return bool
     */
    public function isPaymentFailed(): bool
    {
        $status = strtolower($this->getStatus() ?? '');
        return in_array($status, ['failed', 'rejected', 'declined', 'error']);
    }

    /**
     * Check if event is pending
     *
     * @return bool
     */
    public function isPaymentPending(): bool
    {
        $status = strtolower($this->getStatus() ?? '');
        return in_array($status, ['pending', 'processing', 'initiated']);
    }

    /**
     * Get a specific field from payload
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->payload[$key] ?? $default;
    }

    /**
     * Convert event to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'type' => $this->getType(),
            'transaction_id' => $this->getTransactionId(),
            'status' => $this->getStatus(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'payload' => $this->payload,
        ];
    }
}

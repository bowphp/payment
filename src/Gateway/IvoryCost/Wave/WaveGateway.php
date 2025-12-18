<?php

namespace Bow\Payment\Gateway\IvoryCost\Wave;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Exceptions\PaymentRequestException;
use Bow\Payment\Exceptions\ConfigurationException;

/**
 * Wave Gateway
 * Implementation of Wave Checkout API
 * @link https://docs.wave.com/checkout
 */
class WaveGateway implements ProcessorGatewayInterface
{
    /**
     * Configuration array
     *
     * @var array
     */
    private array $config;

    /**
     * Wave API client
     *
     * @var WaveClient
     */
    private WaveClient $client;

    /**
     * Create a new Wave gateway
     *
     * @param array $config
     * @throws ConfigurationException
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->validateConfig();
        $this->client = new WaveClient($this->config['api_key']);
    }

    /**
     * Make payment - Create a Wave checkout session
     *
     * @param mixed ...$args
     * @return array
     * @throws PaymentRequestException
     * 
     * Expected parameters:
     * - amount: (required) Amount to collect
     * - notify_url: (required) Redirect URL on notification
     * - success_url: (required) Redirect URL on success
     * - error_url: (required) Redirect URL on error
     * - currency: (optional) Currency code (default: XOF)
     * - client_reference: (optional) Your unique reference (max 255 chars)
     * - restrict_payer_mobile: (optional) Phone number (E.164 format)
     * - aggregated_merchant_id: (optional) For aggregators only
     * - idempotency_key: (optional) Unique key to prevent duplicate payments (auto-generated if not provided)
     */
    public function payment(...$args)
    {
        // Validate required fields
        $this->validatePaymentData($args);

        // Generate idempotency key if not provided (prevents duplicate payments)
        $idempotencyKey = $args['idempotency_key'] ?? $this->generateIdempotencyKey();

        // Create checkout session
        $session = $this->client->createCheckoutSession(
            [
                'amount' => $args['amount'],
                'currency' => $args['currency'] ?? 'XOF',
                'notify_url' => $args['notify_url'],
                'success_url' => $args['success_url'],
                'error_url' => $args['error_url'],
                'client_reference' => $args['client_reference'] ?? null,
                'restrict_payer_mobile' => $args['restrict_payer_mobile'] ?? null,
                'aggregated_merchant_id' => $args['aggregated_merchant_id'] ?? null,
            ],
            $idempotencyKey
        );

        return [
            'success' => true,
            'session_id' => $session->getId(),
            'wave_launch_url' => $session->getWaveLaunchUrl(),
            'amount' => $session->getAmount(),
            'currency' => $args['currency'] ?? 'XOF',
            'checkout_status' => $session->getCheckoutStatus(),
            'payment_status' => $session->getPaymentStatus(),
            'transaction_id' => $session->getTransactionId(),
            'client_reference' => $session->getClientReference(),
            'when_expires' => $session->toArray()['when_expires'],
            'idempotency_key' => $idempotencyKey,
            'session' => $session,
        ];
    }

    /**
     * Verify payment - Retrieve checkout session status
     *
     * @param mixed ...$args
     * @return WavePaymentStatus
     * @throws PaymentRequestException
     * 
     * Expected parameters:
     * - session_id: (optional) Checkout session ID
     * - transaction_id: (optional) Transaction ID
     * - client_reference: (optional) Your unique reference
     * 
     * Note: Provide at least one of the above identifiers
     */
    public function verify(...$args)
    {
        $session = null;

        // Try to retrieve by session ID
        if (isset($args['session_id'])) {
            $session = $this->client->retrieveCheckoutSession($args['session_id']);
        }
        // Try to retrieve by transaction ID
        elseif (isset($args['transaction_id'])) {
            $session = $this->client->retrieveCheckoutByTransactionId($args['transaction_id']);
        }
        // Try to search by client reference
        elseif (isset($args['client_reference'])) {
            $sessions = $this->client->searchCheckoutSessions($args['client_reference']);
            if (empty($sessions)) {
                throw new PaymentRequestException('No checkout session found with the provided client reference');
            }
            $session = $sessions[0]; // Get the first matching session
        } else {
            throw new PaymentRequestException(
                'Please provide one of: session_id, transaction_id, or client_reference'
            );
        }

        return new WavePaymentStatus($session);
    }

    /**
     * Make transfer - Not supported by Wave Checkout API
     *
     * @param mixed ...$args
     * @return mixed
     * @throws PaymentRequestException
     */
    public function transfer(...$args)
    {
        throw new PaymentRequestException(
            'Wave transfer is not supported by the Checkout API. Use Wave Business API instead.'
        );
    }

    /**
     * Get balance - Not supported by Wave Checkout API
     *
     * @param mixed ...$args
     * @return mixed
     * @throws PaymentRequestException
     */
    public function balance(...$args)
    {
        throw new PaymentRequestException(
            'Wave balance inquiry is not supported by the Checkout API. Use Wave Business API instead.'
        );
    }

    /**
     * Refund a checkout session
     *
     * @param string $sessionId
     * @return bool
     * @throws PaymentRequestException
     */
    public function refund(string $sessionId): bool
    {
        return $this->client->refundCheckoutSession($sessionId);
    }

    /**
     * Expire a checkout session
     *
     * @param string $sessionId
     * @return bool
     * @throws PaymentRequestException
     */
    public function expire(string $sessionId): bool
    {
        return $this->client->expireCheckoutSession($sessionId);
    }

    /**
     * Search for checkout sessions by client reference
     *
     * @param string $clientReference
     * @return array
     * @throws PaymentRequestException
     */
    public function search(string $clientReference): array
    {
        return $this->client->searchCheckoutSessions($clientReference);
    }

    /**
     * Generate a unique idempotency key
     * Uses UUID v4 format to ensure uniqueness and prevent duplicate payments
     *
     * @return string
     */
    private function generateIdempotencyKey(): string
    {
        // Generate UUID v4 format
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Validate configuration
     *
     * @throws ConfigurationException
     */
    private function validateConfig(): void
    {
        if (empty($this->config['api_key'])) {
            throw new ConfigurationException(
                'Wave API key is required. Please set "api_key" in your Wave configuration.'
            );
        }

        // Validate API key format
        if (!str_starts_with($this->config['api_key'], 'wave_')) {
            throw new ConfigurationException(
                'Invalid Wave API key format. API key should start with "wave_".'
            );
        }
    }

    /**
     * Validate payment data
     *
     * @param array $data
     * @throws PaymentRequestException
     */
    private function validatePaymentData(array $data): void
    {
        if (!isset($data['amount']) || empty($data['amount'])) {
            throw new PaymentRequestException('Amount is required for Wave payment');
        }

        if (!isset($data['success_url']) || empty($data['success_url'])) {
            throw new PaymentRequestException('Success URL is required for Wave payment');
        }

        if (!isset($data['error_url']) || empty($data['error_url'])) {
            throw new PaymentRequestException('Error URL is required for Wave payment');
        }

        // Validate URLs are HTTPS
        if (!str_starts_with($data['success_url'], 'https://')) {
            throw new PaymentRequestException('Success URL must use HTTPS protocol');
        }

        if (!str_starts_with($data['error_url'], 'https://')) {
            throw new PaymentRequestException('Error URL must use HTTPS protocol');
        }

        // Validate amount is positive
        if (floatval($data['amount']) <= 0) {
            throw new PaymentRequestException('Amount must be greater than zero');
        }

        // Validate currency if provided
        if (isset($data['currency'])) {
            $currency = strtoupper($data['currency']);
            if ($currency === 'XOF') {
                // XOF doesn't allow decimals
                if (strpos((string) $data['amount'], '.') !== false) {
                    throw new PaymentRequestException(
                        'XOF currency does not allow decimal places. Amount must be a whole number.'
                    );
                }
            }
        }
    }
}

<?php

namespace Bow\Payment\Shared\Wave;

use Bow\Payment\Common\ProcessorGatewayInterface;
use Bow\Payment\Exceptions\PaymentRequestException;
use Bow\Payment\Exceptions\ConfigurationException;
use Bow\Payment\Exceptions\InputValidationException;

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
     * @param array $params
     * @return array
     * @throws PaymentRequestException
     * 
     * Expected parameters:
     * - amount: (required) Amount to collect
     * - reference: (optional) Your unique reference
     * - currency: (optional) Currency code (default: XOF)
     * - optoons: (optional) Additional payment options
     *      - notify_url: (required) Redirect URL on notification
     *      - success_url: (required) Redirect URL on success
     *      - error_url: (required) Redirect URL on error
     *      - restrict_payer_mobile: (optional) Phone number (E.164 format)
     *      - aggregated_merchant_id: (optional) For aggregators only
     *      - idempotency_key: (optional) Unique key to prevent duplicate payments (auto-generated if not provided)
     */
    public function payment(array $params): array
    {
        if (!isset($params['options']) || !is_array($params['options'])) {
            $params['options'] = [];
        }

        // Merge default options from config
        $params['options'] = array_merge(
            $this->config['options'] ?? [],
            $params['options']
        );

        // Validate required fields
        $this->validatePaymentData($params);

        // Generate idempotency key if not provided (prevents duplicate payments)
        $idempotencyKey = $params['options']['idempotency_key'] ?? $this->generateIdempotencyKey();

        // Create checkout session
        $session = $this->client->createCheckoutSession(
            [
                'amount' => $params['amount'],
                'currency' => $params['currency'] ?? 'XOF',
                'client_reference' => $params['reference'] ?? null,
                'notify_url' => $params['options']['notify_url'] ?? null,
                'success_url' => $params['options']['success_url'] ?? null,
                'error_url' => $params['options']['error_url'] ?? null,
                'restrict_payer_mobile' => $params['options']['restrict_payer_mobile'] ?? false,
                'aggregated_merchant_id' => $params['options']['aggregated_merchant_id'] ?? null,
            ],
            $idempotencyKey
        );

        return [
            'status' => 'success',
            'reference' => $params['reference'],
            'payment_url' => $session->getWaveLaunchUrl(),
            'provider' => 'wave',
            'provider_transaction_id' => $session->getTransactionId(),
            'provider_status' => $session->getPaymentStatus(),
            'provider_data' => [
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
            ],
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
     * - reference: (optional) Your unique reference
     * 
     * Note: Provide at least one of the above identifiers
     */
    public function verify(array $params)
    {
        $session = null;

        // Try to retrieve by session ID
        if (isset($params['session_id'])) {
            $session = $this->client->retrieveCheckoutSession($params['session_id']);
        }
        // Try to retrieve by transaction ID
        elseif (isset($params['transaction_id'])) {
            $session = $this->client->retrieveCheckoutByTransactionId($params['transaction_id']);
        }
        // Try to search by client reference
        elseif (isset($params['client_reference'])) {
            $sessions = $this->client->searchCheckoutSessions($params['client_reference']);
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
     * @throws InputValidationException
     */
    public function validatePaymentData(array $data): void
    {
        if (!isset($data['amount']) || empty($data['amount'])) {
            throw new InputValidationException('Amount is required for Wave payment');
        }

        if (!isset($data['reference']) || empty($data['reference'])) {
            throw new InputValidationException('Reference is required for Wave payment');
        }

        if (!isset($data['currency']) || empty($data['currency'])) {
            throw new InputValidationException('Currency is required for Wave payment');
        }

        // Validate URLs are HTTPS
        if (isset($data['options']['success_url']) && !str_starts_with($data['options']['success_url'], 'https://')) {
            throw new InputValidationException('Success URL must use HTTPS protocol');
        }

        if (isset($data['options']['error_url']) && !str_starts_with($data['options']['error_url'], 'https://')) {
            throw new InputValidationException('Error URL must use HTTPS protocol');
        }

        if (isset($data['options']['cancel_url']) && !str_starts_with($data['options']['cancel_url'], 'https://')) {
            throw new InputValidationException('Cancel URL must use HTTPS protocol');
        }

        // Validate amount is positive
        if (floatval($data['amount']) <= 0) {
            throw new InputValidationException('Amount must be greater than zero');
        }

        // Validate currency if provided
        if (isset($data['currency'])) {
            $currency = strtoupper($data['currency']);
            if ($currency === 'XOF') {
                // XOF doesn't allow decimals
                if (strpos((string) $data['amount'], '.') !== false) {
                    throw new InputValidationException(
                        'XOF currency does not allow decimal places. Amount must be a whole number.'
                    );
                }
            }
        }
    }
}

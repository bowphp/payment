<?php

namespace Bow\Payment\Gateway\IvoryCost\Wave;

use GuzzleHttp\Client as HttpClient;
use Bow\Payment\Exceptions\PaymentRequestException;

/**
 * Wave API Client
 * Handles communication with Wave Checkout API
 */
class WaveClient
{
    /**
     * Base URL for Wave API
     */
    private const BASE_URL = 'https://api.wave.com';

    /**
     * HTTP client instance
     *
     * @var HttpClient
     */
    private HttpClient $http;

    /**
     * WaveClient constructor
     *
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->http = new HttpClient([
            'base_uri' => self::BASE_URL,
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Create a checkout session
     *
     * @param array $data
     * @param string|null $idempotencyKey Unique key to prevent duplicate payments
     * @return WaveCheckoutSession
     * @throws PaymentRequestException
     */
    public function createCheckoutSession(array $data, ?string $idempotencyKey = null): WaveCheckoutSession
    {
        try {
            $headers = [];

            if ($idempotencyKey !== null) {
                $headers['Idempotency-Key'] = $idempotencyKey;
            }

            $response = $this->http->post('/v1/checkout/sessions', [
                'json' => $this->buildCheckoutPayload($data),
                'headers' => $headers,
            ]);

            $content = $response->getBody()->getContents();
            $sessionData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new PaymentRequestException('Invalid JSON response from Wave API');
            }

            return WaveCheckoutSession::fromResponse($sessionData);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->handleClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new PaymentRequestException(
                'Wave API server error: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new PaymentRequestException(
                'Failed to create Wave checkout session: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Retrieve a checkout session by ID
     *
     * @param string $sessionId
     * @return WaveCheckoutSession
     * @throws PaymentRequestException
     */
    public function retrieveCheckoutSession(string $sessionId): WaveCheckoutSession
    {
        try {
            $response = $this->http->get("/v1/checkout/sessions/{$sessionId}");

            $content = $response->getBody()->getContents();
            $sessionData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new PaymentRequestException('Invalid JSON response from Wave API');
            }

            return WaveCheckoutSession::fromResponse($sessionData);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->handleClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new PaymentRequestException(
                'Wave API server error: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new PaymentRequestException(
                'Failed to retrieve Wave checkout session: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Retrieve a checkout session by transaction ID
     *
     * @param string $transactionId
     * @return WaveCheckoutSession
     * @throws PaymentRequestException
     */
    public function retrieveCheckoutByTransactionId(string $transactionId): WaveCheckoutSession
    {
        try {
            $response = $this->http->get('/v1/checkout/sessions', [
                'query' => ['transaction_id' => $transactionId],
            ]);

            $content = $response->getBody()->getContents();
            $sessionData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new PaymentRequestException('Invalid JSON response from Wave API');
            }

            return WaveCheckoutSession::fromResponse($sessionData);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->handleClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new PaymentRequestException(
                'Wave API server error: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new PaymentRequestException(
                'Failed to retrieve Wave checkout session: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Search for checkout sessions by client reference
     *
     * @param string $clientReference
     * @return array
     * @throws PaymentRequestException
     */
    public function searchCheckoutSessions(string $clientReference): array
    {
        try {
            $response = $this->http->get('/v1/checkout/sessions/search', [
                'query' => ['client_reference' => $clientReference],
            ]);

            $content = $response->getBody()->getContents();
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new PaymentRequestException('Invalid JSON response from Wave API');
            }

            $sessions = [];
            foreach ($data['result'] ?? [] as $sessionData) {
                $sessions[] = WaveCheckoutSession::fromResponse($sessionData);
            }

            return $sessions;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->handleClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new PaymentRequestException(
                'Wave API server error: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new PaymentRequestException(
                'Failed to search Wave checkout sessions: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Refund a checkout session
     *
     * @param string $sessionId
     * @return bool
     * @throws PaymentRequestException
     */
    public function refundCheckoutSession(string $sessionId): bool
    {
        try {
            $response = $this->http->post("/v1/checkout/sessions/{$sessionId}/refund");
            return $response->getStatusCode() === 200;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->handleClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new PaymentRequestException(
                'Wave API server error: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new PaymentRequestException(
                'Failed to refund Wave checkout session: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Expire a checkout session
     *
     * @param string $sessionId
     * @return bool
     * @throws PaymentRequestException
     */
    public function expireCheckoutSession(string $sessionId): bool
    {
        try {
            $response = $this->http->post("/v1/checkout/sessions/{$sessionId}/expire");
            return $response->getStatusCode() === 200;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $this->handleClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new PaymentRequestException(
                'Wave API server error: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        } catch (\Exception $e) {
            throw new PaymentRequestException(
                'Failed to expire Wave checkout session: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Build checkout payload from input data
     *
     * @param array $data
     * @return array
     */
    private function buildCheckoutPayload(array $data): array
    {
        $payload = [
            'amount' => $this->formatAmount($data['amount']),
            'currency' => $data['currency'] ?? 'XOF',
            'error_url' => $data['error_url'],
            'success_url' => $data['success_url'],
        ];

        // Optional fields
        if (isset($data['client_reference'])) {
            $payload['client_reference'] = substr($data['client_reference'], 0, 255);
        }

        if (isset($data['restrict_payer_mobile'])) {
            $payload['restrict_payer_mobile'] = $this->formatPhoneNumber($data['restrict_payer_mobile']);
        }

        if (isset($data['aggregated_merchant_id'])) {
            $payload['aggregated_merchant_id'] = $data['aggregated_merchant_id'];
        }

        return $payload;
    }

    /**
     * Format amount according to Wave API requirements
     *
     * @param mixed $amount
     * @return string
     */
    private function formatAmount($amount): string
    {
        // Convert to string and ensure proper decimal formatting
        $formatted = (string) $amount;

        // Remove any leading zeros (except for values < 1)
        if (floatval($formatted) >= 1) {
            $formatted = ltrim($formatted, '0');
        }

        // Ensure max 2 decimal places
        if (strpos($formatted, '.') !== false) {
            $parts = explode('.', $formatted);
            if (strlen($parts[1]) > 2) {
                $formatted = $parts[0] . '.' . substr($parts[1], 0, 2);
            }
        }

        return $formatted;
    }

    /**
     * Format phone number to E.164 standard
     *
     * @param string $phone
     * @return string
     */
    private function formatPhoneNumber(string $phone): string
    {
        // If already starts with +, return as is
        if (strpos($phone, '+') === 0) {
            return $phone;
        }

        // Add country code if missing (default to Ivory Coast +225)
        if (strpos($phone, '225') !== 0) {
            $phone = '225' . ltrim($phone, '0');
        }

        return '+' . $phone;
    }

    /**
     * Handle client exceptions from Wave API
     *
     * @param \GuzzleHttp\Exception\ClientException $e
     * @throws PaymentRequestException
     * @return never
     */
    private function handleClientException(\GuzzleHttp\Exception\ClientException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        $errorData = json_decode($body, true);
        $errorMessage = $errorData['error_message'] ?? $e->getMessage();
        $errorCode = $errorData['error_code'] ?? 'unknown';

        $message = match ($statusCode) {
            400 => "Bad request: {$errorMessage} (Code: {$errorCode})",
            401 => "Authentication failed: {$errorMessage}",
            403 => "Access forbidden: {$errorMessage}",
            404 => "Checkout session not found: {$errorMessage}",
            409 => "Conflict: {$errorMessage}",
            default => "Wave API error ({$statusCode}): {$errorMessage}",
        };

        throw new PaymentRequestException($message, $statusCode, $e);
    }
}

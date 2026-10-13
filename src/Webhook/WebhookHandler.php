<?php

namespace Bow\Payment\Webhook;

use Bow\Payment\Exceptions\PaymentException;

/**
 * Webhook handler for payment providers
 */
class WebhookHandler
{
    /**
     * Webhook secret for signature validation
     *
     * @var string
     */
    private $secret;

    /**
     * Provider name
     *
     * @var string
     */
    private $provider;

    /**
     * Create a new webhook handler
     *
     * @param string $provider
     * @param string $secret
     */
    public function __construct(string $provider, string $secret)
    {
        $this->provider = $provider;
        $this->secret = $secret;
    }

    /**
     * Handle incoming webhook request
     *
     * @param array $payload
     * @param string|null $signature
     * @return WebhookEvent
     * @throws PaymentException
     */
    public function handle(array $payload, ?string $signature = null, ?string $rawBody = null): WebhookEvent
    {
        // The signature is mandatory. An unsigned webhook must never be trusted,
        // otherwise anyone can POST a forged "payment success" event (fraud).
        if (!is_string($signature) || $signature === '') {
            throw new PaymentException('Missing webhook signature', 401);
        }

        // Verify against the exact bytes the provider signed when the raw body
        // is available; re-encoding the decoded array changes key order and
        // escaping and would never match a real gateway signature.
        $signed = $rawBody ?? $payload;

        if (!$this->validateSignature($signed, $signature)) {
            throw new PaymentException('Invalid webhook signature', 401);
        }

        return new WebhookEvent($this->provider, $payload);
    }

    /**
     * Validate webhook signature
     *
     * @param array $payload
     * @param string $signature
     * @return bool
     */
    public function validateSignature(string|array $signed, string $signature): bool
    {
        // Prefer the raw request body (string); fall back to canonical JSON for
        // callers that only have the decoded array.
        $message = is_array($signed) ? json_encode($signed) : $signed;

        $expectedSignature = hash_hmac('sha256', (string) $message, $this->secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Parse raw webhook request
     *
     * @return array
     */
    public static function parseRequest(): array
    {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        return [
            'payload' => $data ?? [],
            'raw' => $body,
            'signature' => $_SERVER['HTTP_X_SIGNATURE'] ?? $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? null,
            'headers' => getallheaders(),
        ];
    }
}

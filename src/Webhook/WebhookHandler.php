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
    public function handle(array $payload, ?string $signature = null): WebhookEvent
    {
        // Validate signature if provided
        if ($signature && !$this->validateSignature($payload, $signature)) {
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
    public function validateSignature(array $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->secret);
        
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
            'signature' => $_SERVER['HTTP_X_SIGNATURE'] ?? $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] ?? null,
            'headers' => getallheaders(),
        ];
    }
}

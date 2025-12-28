<?php

namespace Bow\Payment\Exceptions;

/**
 * Exception thrown when token generation fails
 */
class TokenGenerationException extends PaymentException
{
    /**
     * Create a new token generation exception
     *
     * @param string $provider
     * @param ?\Exception $previous
     */
    public function __construct(string $provider = '', ?\Exception $previous = null)
    {
        $message = $provider
            ? "Failed to generate authentication token for {$provider}"
            : "Failed to generate authentication token";

        parent::__construct($message, 401, $previous);
    }
}

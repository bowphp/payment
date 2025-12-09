<?php

namespace Bow\Payment\Exceptions;

/**
 * Exception thrown when an invalid or unsupported payment provider is requested
 */
class InvalidProviderException extends PaymentException
{
    /**
     * Create a new invalid provider exception
     *
     * @param string $provider
     * @param string $country
     */
    public function __construct(string $provider, string $country = '')
    {
        $message = $country
            ? "The payment provider [{$provider}] is not supported in country [{$country}]."
            : "The payment provider [{$provider}] is not supported.";

        parent::__construct($message, 400);
    }
}

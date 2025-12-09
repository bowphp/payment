<?php

namespace Bow\Payment\Exceptions;

/**
 * Exception thrown when payment configuration is invalid or missing
 */
class ConfigurationException extends PaymentException
{
    /**
     * Create a new configuration exception
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct("Configuration error: {$message}", 500);
    }
}

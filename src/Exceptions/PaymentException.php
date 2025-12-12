<?php

namespace Bow\Payment\Exceptions;

use Exception;

/**
 * Base exception for all payment-related errors
 */
class PaymentException extends Exception
{
    /**
     * Create a new payment exception
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = "", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

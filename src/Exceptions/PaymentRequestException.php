<?php

namespace Bow\Payment\Exceptions;

/**
 * Exception thrown when a payment request fails
 */
class PaymentRequestException extends PaymentException
{
    /**
     * Create a new payment request exception
     *
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message, int $code = 500, \Exception $previous = null)
    {
        parent::__construct("Payment request failed: {$message}", $code, $previous);
    }
}

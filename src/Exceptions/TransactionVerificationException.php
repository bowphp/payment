<?php

namespace Bow\Payment\Exceptions;

/**
 * Exception thrown when transaction verification fails
 */
class TransactionVerificationException extends PaymentException
{
    /**
     * Create a new transaction verification exception
     *
     * @param string $transactionId
     * @param \Exception|null $previous
     */
    public function __construct(string $transactionId = '', \Exception $previous = null)
    {
        $message = $transactionId
            ? "Failed to verify transaction [{$transactionId}]"
            : "Failed to verify transaction";

        parent::__construct($message, 422, $previous);
    }
}

<?php

namespace Bow\Payment\Exceptions;

use Bow\Payment\Exceptions\PaymentException;

class InputValidationException extends PaymentException
{
    /**
     * Create a new input validation exception
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct("Input validation error: {$message}", 422);
    }
}

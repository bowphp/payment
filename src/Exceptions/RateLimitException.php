<?php

namespace Bow\Payment\Exceptions;

/**
 * Exception thrown when rate limit is exceeded
 */
class RateLimitException extends PaymentException
{
    /**
     * Create a new rate limit exception
     *
     * @param int $retryAfter Seconds until retry is allowed
     */
    public function __construct(int $retryAfter = 60)
    {
        parent::__construct(
            "Rate limit exceeded. Please try again in {$retryAfter} seconds.",
            429
        );
    }
}

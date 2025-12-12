<?php

namespace Bow\Payment\Support;

use Bow\Payment\Exceptions\PaymentRequestException;

/**
 * Retry logic for failed API calls
 */
class RetryHandler
{
    /**
     * Maximum number of retry attempts
     *
     * @var int
     */
    private $maxAttempts;

    /**
     * Delay between retries in milliseconds
     *
     * @var int
     */
    private $retryDelay;

    /**
     * Whether to use exponential backoff
     *
     * @var bool
     */
    private $exponentialBackoff;

    /**
     * Transaction logger
     *
     * @var TransactionLogger|null
     */
    private $logger;

    /**
     * Create a new retry handler
     *
     * @param int $maxAttempts
     * @param int $retryDelay Delay in milliseconds
     * @param bool $exponentialBackoff
     * @param TransactionLogger|null $logger
     */
    public function __construct(
        int $maxAttempts = 3,
        int $retryDelay = 1000,
        bool $exponentialBackoff = true,
        ?TransactionLogger $logger = null
    ) {
        $this->maxAttempts = $maxAttempts;
        $this->retryDelay = $retryDelay;
        $this->exponentialBackoff = $exponentialBackoff;
        $this->logger = $logger;
    }

    /**
     * Execute a callable with retry logic
     *
     * @param callable $callback
     * @param array $retryableExceptions List of exception classes to retry on
     * @return mixed
     * @throws PaymentRequestException
     */
    public function execute(callable $callback, array $retryableExceptions = [])
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxAttempts) {
            try {
                $attempt++;
                
                if ($this->logger && $attempt > 1) {
                    $this->logger->info("Retry attempt {$attempt}/{$this->maxAttempts}");
                }

                return $callback();
            } catch (\Exception $e) {
                $lastException = $e;

                // Check if this exception is retryable
                $shouldRetry = empty($retryableExceptions) || $this->isRetryableException($e, $retryableExceptions);

                if (!$shouldRetry || $attempt >= $this->maxAttempts) {
                    if ($this->logger) {
                        $this->logger->error("Request failed after {$attempt} attempts", [
                            'exception' => get_class($e),
                            'message' => $e->getMessage(),
                        ]);
                    }
                    throw new PaymentRequestException(
                        $e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }

                // Calculate delay
                $delay = $this->exponentialBackoff
                    ? $this->retryDelay * pow(2, $attempt - 1)
                    : $this->retryDelay;

                if ($this->logger) {
                    $this->logger->warning("Request failed, retrying in {$delay}ms", [
                        'attempt' => $attempt,
                        'exception' => get_class($e),
                    ]);
                }

                // Wait before retry
                usleep($delay * 1000);
            }
        }

        throw new PaymentRequestException(
            "Failed after {$this->maxAttempts} attempts: " . $lastException->getMessage()
        );
    }

    /**
     * Check if an exception is retryable
     *
     * @param \Exception $exception
     * @param array $retryableExceptions
     * @return bool
     */
    private function isRetryableException(\Exception $exception, array $retryableExceptions): bool
    {
        foreach ($retryableExceptions as $retryableClass) {
            if ($exception instanceof $retryableClass) {
                return true;
            }
        }
        return false;
    }
}

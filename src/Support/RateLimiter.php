<?php

namespace Bow\Payment\Support;

use Bow\Payment\Exceptions\RateLimitException;

/**
 * Rate limiter for API calls
 */
class RateLimiter
{
    /**
     * Maximum number of requests allowed
     *
     * @var int
     */
    private $maxRequests;

    /**
     * Time window in seconds
     *
     * @var int
     */
    private $timeWindow;

    /**
     * Storage for request timestamps
     *
     * @var array
     */
    private $requests = [];

    /**
     * Cache file path
     *
     * @var string
     */
    private $cacheFile;

    /**
     * Create a new rate limiter
     *
     * @param int $maxRequests Maximum requests allowed
     * @param int $timeWindow Time window in seconds
     * @param string $cacheFile Path to cache file
     */
    public function __construct(
        int $maxRequests = 60,
        int $timeWindow = 60,
        string $cacheFile = ''
    ) {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        $this->cacheFile = $cacheFile ?: sys_get_temp_dir() . '/bow_payment_rate_limit.cache';

        $this->loadRequests();
    }

    /**
     * Check if a request is allowed
     *
     * @param string $key Unique identifier for the rate limit (e.g., provider name)
     * @return bool
     */
    public function isAllowed(string $key): bool
    {
        $this->cleanExpiredRequests($key);

        if (!isset($this->requests[$key])) {
            $this->requests[$key] = [];
        }

        return count($this->requests[$key]) < $this->maxRequests;
    }

    /**
     * Record a request
     *
     * @param string $key
     * @return void
     * @throws RateLimitException
     */
    public function hit(string $key): void
    {
        if (!$this->isAllowed($key)) {
            $retryAfter = $this->getRetryAfter($key);
            throw new RateLimitException($retryAfter);
        }

        if (!isset($this->requests[$key])) {
            $this->requests[$key] = [];
        }

        $this->requests[$key][] = time();
        $this->saveRequests();
    }

    /**
     * Get seconds until retry is allowed
     *
     * @param string $key
     * @return int
     */
    public function getRetryAfter(string $key): int
    {
        if (!isset($this->requests[$key]) || empty($this->requests[$key])) {
            return 0;
        }

        $oldestRequest = min($this->requests[$key]);
        $retryAfter = ($oldestRequest + $this->timeWindow) - time();

        return max(0, $retryAfter);
    }

    /**
     * Remove expired requests from the list
     *
     * @param string $key
     * @return void
     */
    private function cleanExpiredRequests(string $key): void
    {
        if (!isset($this->requests[$key])) {
            return;
        }

        $now = time();
        $this->requests[$key] = array_filter(
            $this->requests[$key],
            fn($timestamp) => ($now - $timestamp) < $this->timeWindow
        );

        $this->saveRequests();
    }

    /**
     * Load requests from cache file
     *
     * @return void
     */
    private function loadRequests(): void
    {
        if (file_exists($this->cacheFile)) {
            $data = file_get_contents($this->cacheFile);
            $this->requests = json_decode($data, true) ?: [];
        }
    }

    /**
     * Save requests to cache file
     *
     * @return void
     */
    private function saveRequests(): void
    {
        file_put_contents($this->cacheFile, json_encode($this->requests), LOCK_EX);
    }

    /**
     * Clear all rate limit data for a key
     *
     * @param string $key
     * @return void
     */
    public function clear(string $key): void
    {
        unset($this->requests[$key]);
        $this->saveRequests();
    }

    /**
     * Clear all rate limit data
     *
     * @return void
     */
    public function clearAll(): void
    {
        $this->requests = [];
        $this->saveRequests();
    }
}

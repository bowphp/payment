<?php

namespace Bow\Payment\Tests;

use PHPUnit\Framework\TestCase;
use Bow\Payment\Support\RateLimiter;
use Bow\Payment\Exceptions\RateLimitException;

class RateLimiterTest extends TestCase
{
    private $cacheFile;
    private $limiter;

    protected function setUp(): void
    {
        $this->cacheFile = sys_get_temp_dir() . '/test_rate_limit_' . uniqid() . '.cache';
        $this->limiter = new RateLimiter(5, 60, $this->cacheFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    public function testAllowsRequestsWithinLimit()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue($this->limiter->isAllowed('test-key'));
            $this->limiter->hit('test-key');
        }
    }

    public function testThrowsExceptionWhenLimitExceeded()
    {
        $this->expectException(RateLimitException::class);
        
        for ($i = 0; $i < 6; $i++) {
            $this->limiter->hit('test-key');
        }
    }

    public function testDifferentKeysAreIndependent()
    {
        $this->limiter->hit('key1');
        $this->limiter->hit('key1');
        
        $this->assertTrue($this->limiter->isAllowed('key2'));
        $this->limiter->hit('key2');
        
        $this->assertTrue($this->limiter->isAllowed('key1'));
    }

    public function testClearRemovesLimitForKey()
    {
        for ($i = 0; $i < 5; $i++) {
            $this->limiter->hit('test-key');
        }
        
        $this->assertFalse($this->limiter->isAllowed('test-key'));
        
        $this->limiter->clear('test-key');
        
        $this->assertTrue($this->limiter->isAllowed('test-key'));
    }
}

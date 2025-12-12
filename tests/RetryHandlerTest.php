<?php

namespace Bow\Payment\Tests;

use PHPUnit\Framework\TestCase;
use Bow\Payment\Support\RetryHandler;
use Bow\Payment\Support\TransactionLogger;
use Bow\Payment\Exceptions\PaymentRequestException;

class RetryHandlerTest extends TestCase
{
    public function testSuccessfulExecution()
    {
        $handler = new RetryHandler(3, 100, false);
        
        $result = $handler->execute(function() {
            return 'success';
        });
        
        $this->assertEquals('success', $result);
    }

    public function testRetryOnFailure()
    {
        $attempts = 0;
        $handler = new RetryHandler(3, 100, false);
        
        $this->expectException(PaymentRequestException::class);
        
        $handler->execute(function() use (&$attempts) {
            $attempts++;
            throw new \Exception('Test failure');
        });
        
        $this->assertEquals(3, $attempts);
    }

    public function testSuccessAfterRetry()
    {
        $attempts = 0;
        $handler = new RetryHandler(3, 100, false);
        
        $result = $handler->execute(function() use (&$attempts) {
            $attempts++;
            if ($attempts < 2) {
                throw new \Exception('Temporary failure');
            }
            return 'success';
        });
        
        $this->assertEquals('success', $result);
        $this->assertEquals(2, $attempts);
    }
}

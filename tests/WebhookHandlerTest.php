<?php

namespace Bow\Payment\Tests;

use PHPUnit\Framework\TestCase;
use Bow\Payment\Webhook\WebhookHandler;
use Bow\Payment\Webhook\WebhookEvent;
use Bow\Payment\Exceptions\PaymentException;

class WebhookHandlerTest extends TestCase
{
    private $handler;
    private $secret = 'test-secret-key';

    protected function setUp(): void
    {
        $this->handler = new WebhookHandler('orange', $this->secret);
    }

    public function testHandleValidWebhook()
    {
        $payload = [
            'event' => 'payment.success',
            'transaction_id' => 'TX-123',
            'status' => 'successful',
            'amount' => 1000,
        ];
        
        $event = $this->handler->handle($payload);
        
        $this->assertInstanceOf(WebhookEvent::class, $event);
        $this->assertEquals('orange', $event->getProvider());
        $this->assertEquals('TX-123', $event->getTransactionId());
    }

    public function testValidateSignature()
    {
        $payload = ['test' => 'data'];
        $validSignature = hash_hmac('sha256', json_encode($payload), $this->secret);
        
        $this->assertTrue($this->handler->validateSignature($payload, $validSignature));
    }

    public function testInvalidSignatureThrowsException()
    {
        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Invalid webhook signature');
        
        $payload = ['test' => 'data'];
        $invalidSignature = 'invalid-signature';
        
        $this->handler->handle($payload, $invalidSignature);
    }
}

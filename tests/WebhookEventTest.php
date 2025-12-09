<?php

namespace Bow\Payment\Tests;

use PHPUnit\Framework\TestCase;
use Bow\Payment\Webhook\WebhookEvent;

class WebhookEventTest extends TestCase
{
    public function testGetBasicEventData()
    {
        $payload = [
            'event' => 'payment.success',
            'transaction_id' => 'TX-123',
            'status' => 'successful',
            'amount' => 1000,
            'currency' => 'XOF',
        ];
        
        $event = new WebhookEvent('orange', $payload);
        
        $this->assertEquals('orange', $event->getProvider());
        $this->assertEquals('payment.success', $event->getType());
        $this->assertEquals('TX-123', $event->getTransactionId());
        $this->assertEquals('successful', $event->getStatus());
        $this->assertEquals(1000.0, $event->getAmount());
        $this->assertEquals('XOF', $event->getCurrency());
    }

    public function testIsPaymentSuccess()
    {
        $event = new WebhookEvent('orange', ['status' => 'successful']);
        $this->assertTrue($event->isPaymentSuccess());
        
        $event = new WebhookEvent('orange', ['status' => 'completed']);
        $this->assertTrue($event->isPaymentSuccess());
        
        $event = new WebhookEvent('orange', ['status' => 'failed']);
        $this->assertFalse($event->isPaymentSuccess());
    }

    public function testIsPaymentFailed()
    {
        $event = new WebhookEvent('orange', ['status' => 'failed']);
        $this->assertTrue($event->isPaymentFailed());
        
        $event = new WebhookEvent('orange', ['status' => 'rejected']);
        $this->assertTrue($event->isPaymentFailed());
        
        $event = new WebhookEvent('orange', ['status' => 'successful']);
        $this->assertFalse($event->isPaymentFailed());
    }

    public function testIsPaymentPending()
    {
        $event = new WebhookEvent('orange', ['status' => 'pending']);
        $this->assertTrue($event->isPaymentPending());
        
        $event = new WebhookEvent('orange', ['status' => 'processing']);
        $this->assertTrue($event->isPaymentPending());
    }

    public function testToArray()
    {
        $payload = [
            'transaction_id' => 'TX-123',
            'status' => 'successful',
            'amount' => 1000,
        ];
        
        $event = new WebhookEvent('orange', $payload);
        $array = $event->toArray();
        
        $this->assertEquals('orange', $array['provider']);
        $this->assertEquals('TX-123', $array['transaction_id']);
        $this->assertEquals('successful', $array['status']);
        $this->assertEquals(1000.0, $array['amount']);
    }

    public function testGetCustomField()
    {
        $event = new WebhookEvent('orange', [
            'custom_field' => 'custom_value',
            'another_field' => 123,
        ]);
        
        $this->assertEquals('custom_value', $event->get('custom_field'));
        $this->assertEquals(123, $event->get('another_field'));
        $this->assertEquals('default', $event->get('nonexistent', 'default'));
    }
}

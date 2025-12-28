<?php

namespace Bow\Payment\Tests;

use PHPUnit\Framework\TestCase;
use Bow\Payment\Payment;
use Bow\Payment\Exceptions\InvalidProviderException;

class PaymentTest extends TestCase
{
    public function testPaymentConstants()
    {
        $this->assertEquals('orange', Payment::ORANGE);
        $this->assertEquals('mtn', Payment::MTN);
        $this->assertEquals('moov', Payment::MOOV);
        $this->assertEquals('wave', Payment::WAVE);
        $this->assertEquals('djamo', Payment::DJAMO);
        $this->assertEquals('ivory_coast', Payment::CI);
    }

    public function testProviderMapping()
    {
        $providers = Payment::CI_PROVIDER;
        
        $this->assertArrayHasKey(Payment::ORANGE, $providers);
        $this->assertArrayHasKey(Payment::MTN, $providers);
        $this->assertArrayHasKey(Payment::MOOV, $providers);
        $this->assertArrayHasKey(Payment::WAVE, $providers);
        $this->assertArrayHasKey(Payment::DJAMO, $providers);
        
        $this->assertEquals(
            \Bow\Payment\Gateway\IvoryCost\OrangeMoney\OrangeMoneyGateway::class,
            $providers[Payment::ORANGE]
        );
        
        $this->assertEquals(
            \Bow\Payment\Gateway\IvoryCost\Mono\MonoGateway::class,
            $providers[Payment::MTN]
        );
    }

    public function testConfigurePayment()
    {
        $config = [
            'default' => [
                'gateway' => Payment::ORANGE,
                'country' => 'ci',
            ],
            'ivory_coast' => [
                'orange' => [
                    'client_key' => 'test_key',
                    'client_secret' => 'test_secret',
                ],
            ],
        ];
        
        $payment = Payment::configure($config);
        
        $this->assertInstanceOf(Payment::class, $payment);
    }
}

<?php

use Bow\Payment\OrangeMoney\OrangeMoney;
use Bow\Payment\OrangeMoney\OrangeMoneyPayment;
use Bow\Payment\OrangeMoney\OrangeMoneyToken;
use Bow\Payment\OrangeMoney\OrangeMoneyTokenGenerator;

class OrangeMoneyTest extends \PHPUnit\Framework\TestCase
{
    public function testGetToken()
    {
        $stub = $this->createMock(OrangeMoneyTokenGenerator::class);
        $stub->expects($this->once())->method('getToken')
            ->willReturn($this->getMockBuilder(OrangeMoneyToken::class)
            ->disableOriginalConstructor()->getMock());

        $this->assertInstanceOf(OrangeMoneyToken::class, $stub->getToken());
    }

    public function testMakePayment()
    {
        $token = $this->getMockBuilder(OrangeMoneyToken::class)
            ->disableOriginalConstructor()->getMock();
        
        $payment = $this->getMockBuilder(OrangeMoneyPayment::class)
            ->setConstructorArgs([$token, 123456])
            ->setMethods(['prepare'])->getMock();

        $payment_status = $this->createMock(OrangeMoney::class);
        $payment->method('prepare')
            ->willReturn($payment_status);

        $this->assertInstanceOf(OrangeMoney::class, $payment->prepare(500, 'reference', 1));
    }
}

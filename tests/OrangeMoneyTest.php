<?php

use Bow\Payment\OrangeMoney\OrangeMoney;
use Bow\Payment\OrangeMoney\OrangeMoneyPaymentStatus;
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
        
        $orange = $this->getMockBuilder(OrangeMoney::class)
            ->setConstructorArgs([$token, 123456])
            ->setMethods(['pay'])->getMock();

        $payment_status = $this->createMock(OrangeMoneyPaymentStatus::class);
        $orange->method('pay')
            ->willReturn($payment_status);

        $this->assertInstanceOf(OrangeMoneyPaymentStatus::class, $orange->pay(500, 'reference', 1));
    }
}

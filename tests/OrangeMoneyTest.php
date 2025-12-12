<?php

use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyPayment;
use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyToken;
use Bow\Payment\IvoryCost\OrangeMoney\OrangeMoneyTokenGenerator;

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

    public function testPreparePayment()
    {
        $token = $this->getMockBuilder(OrangeMoneyToken::class)
            ->disableOriginalConstructor()->getMock();
        
        $payment = $this->getMockBuilder(OrangeMoneyPayment::class)
            ->setConstructorArgs([$token, 123456])
            ->setMethods(['prepare'])->getMock();

        $payment_status = $this->createMock(OrangeMoneyPayment::class);
        $payment->method('prepare')->willReturn($payment_status);

        $this->assertInstanceOf(OrangeMoneyPayment::class, $payment->prepare(500, 'reference', 1));
    }

    public function testMakePayment()
    {
        $orange = $this->getMockBuilder(OrangeMoneyPayment::class)
            ->disableOriginalConstructor()->setMethods(['pay'])->getMock();

        $orange->method('pay')->willReturn(true);

        $this->assertTrue($orange->pay());
    }
}

<?php

use Bow\Payment\Gateway\IvoryCost\Orange\OrangePayment;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeToken;
use Bow\Payment\Gateway\IvoryCost\Orange\OrangeTokenGenerator;

class OrangeTest extends \PHPUnit\Framework\TestCase
{
    public function testGetToken()
    {
        $stub = $this->createMock(OrangeTokenGenerator::class);
        $stub->expects($this->once())->method('getToken')
            ->willReturn($this->getMockBuilder(OrangeToken::class)
            ->disableOriginalConstructor()->getMock());

        $this->assertInstanceOf(OrangeToken::class, $stub->getToken());
    }

    public function testPreparePayment()
    {
        $token = $this->getMockBuilder(OrangeToken::class)
            ->disableOriginalConstructor()->getMock();
        
        $payment = $this->getMockBuilder(OrangePayment::class)
            ->setConstructorArgs([$token, 123456])
            ->setMethods(['prepare'])->getMock();

        $payment_status = $this->createMock(OrangePayment::class);
        $payment->method('prepare')->willReturn($payment_status);

        $this->assertInstanceOf(OrangePayment::class, $payment->prepare(500, 'reference', 1));
    }

    public function testMakePayment()
    {
        $orange = $this->getMockBuilder(OrangePayment::class)
            ->disableOriginalConstructor()->setMethods(['pay'])->getMock();

        $orange->method('pay')->willReturn(true);

        $this->assertTrue($orange->pay());
    }
}

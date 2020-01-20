<?php

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
}

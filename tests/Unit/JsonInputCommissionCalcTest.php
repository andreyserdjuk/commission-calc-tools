<?php

declare(strict_types=1);

namespace CommissionCalc\Tests\Unit;

use CommissionCalc\CommissionCalcInterface;
use CommissionCalc\Exception\BadPaymentDataException;
use CommissionCalc\JsonInputCommissionCalc;
use PHPUnit\Framework\MockObject\MockObject;

class JsonInputCommissionCalcTest extends BaseTestCase
{
    /**
     * @covers \CommissionCalc\JsonInputCommissionCalc::__construct()
     * @covers \CommissionCalc\JsonInputCommissionCalc::calcCommission()
     */
    public function testCalcCommission()
    {
        $expectedCommission = '1.00';
        $paymentData = '{"bin":"45717360","amount":"100.00","currency":"EUR"}';

        /** @var MockObject|CommissionCalcInterface $commissionCalcMock */
        $commissionCalcMock = $this->getMockBuilder(CommissionCalcInterface::class)
            ->onlyMethods(['calcCommission'])
            ->getMock()
        ;

        $commissionCalcMock
            ->expects($this->once())
            ->method('calcCommission')
            ->with(
                $this->equalTo('45717360'),
                $this->equalTo('100.00'),
                $this->equalTo('EUR')
            )
            ->willReturn($expectedCommission)
        ;

        $jsonCalc = new JsonInputCommissionCalc($this->getSerializer(), $commissionCalcMock);
        $commission = $jsonCalc->calcCommission($paymentData);

        $this->assertEquals($expectedCommission, $commission);
    }

    /**
     * @covers \CommissionCalc\JsonInputCommissionCalc::__construct()
     * @covers \CommissionCalc\JsonInputCommissionCalc::calcCommission()
     */
    public function testBadPaymentData()
    {
        $this->expectException(BadPaymentDataException::class);

        $paymentData = '{"amount":"100.00","currency":"EUR"}';
        /** @var MockObject|CommissionCalcInterface $commissionCalcMock */
        $commissionCalcMock = $this->getMockBuilder(CommissionCalcInterface::class)
            ->onlyMethods(['calcCommission'])
            ->getMock()
        ;

        $commissionCalcMock
            ->expects($this->never())
            ->method('calcCommission')
        ;

        $jsonCalc = new JsonInputCommissionCalc($this->getSerializer(), $commissionCalcMock);
        $jsonCalc->calcCommission($paymentData);
    }
}

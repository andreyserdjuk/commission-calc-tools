<?php

declare(strict_types=1);

namespace CommissionCalc\Tests\Unit;

use CommissionCalc\BinProviderInterface;
use CommissionCalc\CurrencyRateProviderInterface;
use CommissionCalc\EurCommissionCalc;
use CommissionCalc\Models\BinData;
use CommissionCalc\Models\BinCountry;
use CommissionCalc\Models\CurrencyRates;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class EurCommissionCalcTest extends TestCase
{
    private const EUR_COMMISSION_RATE = '0.01';
    private const NON_EUR_COMMISSION_RATE = '0.02';

    /**
     * @dataProvider calcCommissionDataProvider
     * @covers       \CommissionCalc\EurCommissionCalc::calcCommission
     * @covers       \CommissionCalc\EurCommissionCalc::isEu
     * @covers       \CommissionCalc\EurCommissionCalc::ceilFloat
     * @covers       \CommissionCalc\EurCommissionCalc::__construct
     */
    public function testCalcCommission(
        string $sourceAmount,
        string $sourceCurrency,
        string $alpha2,
        ?float $rate,
        string $expectedCommission,
        $expectedException
    ) {
        if ($expectedException !== null) {
            $this->expectException($expectedException);
        }

        $binProvider = $this->getBinProviderMock($alpha2);
        $currencyRateProvider = $this->getCurrencyRateProviderMock($rate);

        $calc = new EurCommissionCalc(
            $binProvider,
            $currencyRateProvider,
            self::EUR_COMMISSION_RATE,
            self::NON_EUR_COMMISSION_RATE,
        );

        $commission = $calc->calcCommission('123123', $sourceAmount, $sourceCurrency);

        $this->assertEquals($expectedCommission, $commission);
    }

    public function calcCommissionDataProvider()
    {
        return [
            [
                '22.11',
                'EUR',
                'AT',
                1,
                '0.23',/** ceil result of 22.11 * 0.01; @see EurCommissionCalcTest::EUR_COMMISSION_RATE */
                null,
            ],
            [
                '10000.00',
                'JPY',
                'US',
                0.1,
                '20.00',/** ceil result of 10000.00 * 0.1 * 0.02; @see EurCommissionCalcTest::NON_EUR_COMMISSION_RATE */
                null,
            ],
            [
                '56.32',
                'EUR',
                'US',
                1,
                '1.13',/** ceil result of 56.32 * 0.02; @see EurCommissionCalcTest::NON_EUR_COMMISSION_RATE */
                null,
            ],
            [
                '10',
                'USD',
                'RO',
                0.9,
                '0.09',/** ceil result of 10 * 0.9 * 0.01; @see EurCommissionCalcTest::EUR_COMMISSION_RATE */
                null,
            ],
            [
                '10',
                'USD',
                'RU',
                0.8,
                '0.16',/** ceil result of 10 * 0.8 * 0.02; @see EurCommissionCalcTest::NON_EUR_COMMISSION_RATE */
                null,
            ],
            [
                '10',
                'USD',
                'RU',
                null,
                '0.00',
                UnexpectedValueException::class,
            ],
        ];
    }

    /**
     * @return BinProviderInterface|MockObject
     */
    private function getBinProviderMock(string $alpha2)
    {
        $country = $this->createConfiguredMock(
            BinCountry::class,
            [
                'getAlpha2' => $alpha2,
            ]
        );

        $binData = $this->createConfiguredMock(
            BinData::class,
            [
                'getCountry' => $country,
            ]
        );

        $binProvider = $this->getMockBuilder(BinProviderInterface::class)
            ->onlyMethods(['getBinData'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $binProvider
            ->method('getBinData')
            ->willReturn($binData)
        ;

        return $binProvider;
    }

    /**
     * @return CurrencyRateProviderInterface|MockObject
     */
    private function getCurrencyRateProviderMock(?float $rate)
    {
        $currencyRateProvider = $this->getMockBuilder(CurrencyRateProviderInterface::class)
            ->onlyMethods(['getRates'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $rates = $this->createConfiguredMock(
            CurrencyRates::class,
            [
                'getRate' => $rate,
            ]
        );

        $currencyRateProvider
            ->method('getRates')
            ->willReturn($rates)
        ;

        return $currencyRateProvider;
    }
}

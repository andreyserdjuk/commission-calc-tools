<?php

namespace Tests\CardAmountCalc\Unit;

use CardAmountCalc\BinProviderInterface;
use CardAmountCalc\CurrencyRateProviderInterface;
use CardAmountCalc\EurCommissionCalc;
use CardAmountCalc\Models\BinData;
use CardAmountCalc\Models\Country;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EurCommissionCalcTest extends TestCase
{
    private const EUR_COMMISSION_RATE = 0.01;
    private const NON_EUR_COMMISSION_RATE = 0.02;

    /**
     * @dataProvider calcCommissionDataProvider
     * @covers \CardAmountCalc\EurCommissionCalc::calcCommission
     * @covers \CardAmountCalc\EurCommissionCalc::__construct
     */
    public function testCalcCommission(
        float $sourceAmount,
        string $sourceCurrency,
        bool $isEu,
        float $rate,
        float $expectedCommission
    ) {
        $binProvider = $this->getBinProviderMock();
        $currencyRateProvider = $this->getCurrencyRateProviderMock($rate);

        /** @var EurCommissionCalc|MockObject $commissionCalc */
        $commissionCalc = $this->getMockBuilder(EurCommissionCalc::class)
            ->onlyMethods(['isEu'])
            ->setConstructorArgs([
                $binProvider,
                $currencyRateProvider,
                self::EUR_COMMISSION_RATE,
                self::NON_EUR_COMMISSION_RATE,
            ])
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $commissionCalc
            ->method('isEu')
            ->willReturn($isEu);

        $commission = $commissionCalc->calcCommission('123123', $sourceAmount, $sourceCurrency);

        $this->assertEquals($expectedCommission, $commission);
    }

    public function calcCommissionDataProvider()
    {
        return [
            [
                22.11,
                'EUR',
                true,
                0,
                0.23, /** ceil result of 22.11 * 0.01; @see EurCommissionCalcTest::EUR_COMMISSION_RATE */
            ],
            [
                56.32,
                'EUR',
                false,
                0,
                1.13, /** ceil result of 56.32 * 0.02; @see EurCommissionCalcTest::NON_EUR_COMMISSION_RATE */
            ],
            [
                10,
                'USD',
                true,
                1.11,
                0.09, /** ceil result of 10 / 1.11 * 0.01; @see EurCommissionCalcTest::EUR_COMMISSION_RATE */
            ],
            [
                10,
                'USD',
                false,
                1.11,
                0.18, /** ceil result of 10 / 1.11 * 0.02; @see EurCommissionCalcTest::NON_EUR_COMMISSION_RATE */
            ],
            [
                10,
                'USD',
                false,
                0,
                0.2, /** ceil result of 10 * 0.02; @see EurCommissionCalcTest::NON_EUR_COMMISSION_RATE */
            ],
        ];
    }

    /**
     * @dataProvider isEuDataProvider
     * @covers \CardAmountCalc\EurCommissionCalc::isEu()
     */
    public function testIsEu($countryCode, $expectedIsEu)
    {
        $commissionCalc = $this->createMock(EurCommissionCalc::class);

        $isEu = (function ($alpha2) {
            return $this->isEu($alpha2);
        })->call($commissionCalc, $countryCode);

        $this->assertEquals($expectedIsEu, $isEu);
    }

    public function isEuDataProvider()
    {
        return [
            [
                'LU',
                true
            ],
            [
                'PO',
                true,
            ],
            [
                'US',
                false,
            ],
            [
                'FR',
                true,
            ],
            [
                'RU',
                false,
            ]
        ];
    }

    /**
     * @return BinProviderInterface|MockObject
     */
    protected function getBinProviderMock()
    {
        $country = $this->createConfiguredMock(Country::class, [
            'getAlpha2' => 'XX'
        ]);

        $binData = $this->createConfiguredMock(BinData::class, [
            'getCountry' => $country,
        ]);

        $binProvider = $this->getMockBuilder(BinProviderInterface::class)
            ->onlyMethods(['getBinData'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $binProvider
            ->method('getBinData')
            ->willReturn($binData);

        return $binProvider;
    }

    /**
     * @return CurrencyRateProviderInterface|MockObject
     */
    public function getCurrencyRateProviderMock(float $rate)
    {
        $currencyRateProvider = $this->getMockBuilder(CurrencyRateProviderInterface::class)
            ->onlyMethods(['getRate'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $currencyRateProvider
            ->method('getRate')
            ->willReturn($rate);

        return $currencyRateProvider;
    }
}

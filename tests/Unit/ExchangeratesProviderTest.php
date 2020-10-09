<?php

namespace Tests\CardAmountCalc\Unit;

use CardAmountCalc\ExchangeratesProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExchangeratesProviderTest extends TestCase
{
    /**
     * @dataProvider ratesDataProvider
     * @covers \CardAmountCalc\ExchangeratesProvider::getRate()
     */
    public function testGetRate(
        ?string $fileGetContentsResult,
        string $curency,
        float $expectedRate,
        bool $isValidServerResponse
    ) {
        if (!$isValidServerResponse) {
            $this->expectException(\UnexpectedValueException::class);
        }

        /** @var ExchangeratesProvider|MockObject $provider */
        $provider = $this->getMockBuilder(ExchangeratesProvider::class)
            ->onlyMethods(['fetchData'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $provider
            ->method('fetchData')
            ->willReturn($fileGetContentsResult);

        $rate = $provider->getRate('EUR', $curency);

        $this->assertEquals($expectedRate, $rate);
    }

    public function ratesDataProvider()
    {
        return [
            [
                '{"rates":{"USD":1.1765,"MXN":25.1418,"ILS":3.9909,"GBP":0.91035},"base":"EUR","date":"2020-10-08"}',
                'USD',
                1.18,
                true,
            ],
            [
                '{"rates":{"CAD":1.5511},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                1.56,
                true,
            ],
            [
                '{"rates":{"CAD":1.5599},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                1.56,
                true,
            ],
            [
                '{"rates":{"CAD":1.5503},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                1.56,
                true,
            ],
            [
                '{"rates":{"CAD":11.503},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                11.51,
                true,
            ],
            [
                '{"rates":{"CAD":11.503},"base":"EUR","date":"2020-10-08"}',
                'USD',
                0.00,
                false,
            ],
            [
                '',
                'CAD',
                0.00,
                false,
            ],
        ];
    }
}

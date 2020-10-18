<?php

declare(strict_types=1);

namespace CommissionCalc\Tests\Unit;

use CommissionCalc\ExchangeRatesClientInterface;
use CommissionCalc\ExchangeratesProvider;
use CommissionCalc\Models\CurrencyRates;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Client\ClientExceptionInterface;
use UnexpectedValueException;

class ExchangeratesProviderTest extends BaseTestCase
{
    /**
     * @dataProvider ratesDataProvider
     * @covers       \CommissionCalc\ExchangeratesProvider::getRates()
     * @covers       \CommissionCalc\ExchangeratesProvider::__construct()
     */
    public function testGetRate(
        string $ratesDataResponse,
        string $currencyCode,
        float $expectedRate,
        bool $isValidServerResponse
    ) {
        if (!$isValidServerResponse) {
            $this->expectException(UnexpectedValueException::class);
        }

        $ratesClientMock = $this->createConfiguredMock(
            ExchangeRatesClientInterface::class,
            [
                'getRatesData' => Utils::streamFor($ratesDataResponse),
            ]
        );

        $provider = new ExchangeratesProvider($ratesClientMock, $this->getSerializer());
        $rates = $provider->getRates('EUR');

        $this->assertInstanceOf(CurrencyRates::class, $rates);
        $this->assertEquals($expectedRate, $rates->getRate($currencyCode));
    }

    /**
     * @covers \CommissionCalc\ExchangeratesProvider::getRates()
     * @covers \CommissionCalc\ExchangeratesProvider::__construct()
     */
    public function testApiServerError()
    {
        $this->expectException(ClientExceptionInterface::class);

        /** @var MockObject|ExchangeRatesClientInterface $clientMock */
        $clientMock = $this->getMockBuilder(ExchangeRatesClientInterface::class)
            ->onlyMethods(['getRatesData'])
            ->getMock();

        $clientMock
            ->method('getRatesData')
            ->willThrowException(new InvalidArgumentException());

        $provider = new ExchangeratesProvider($clientMock, $this->getSerializer());
        $provider->getRates('EUR');
    }

    public function ratesDataProvider()
    {
        return [
            [
                '{"rates":{"USD":1.1765,"MXN":25.1418,"ILS":3.9909,"GBP":0.91035},"base":"EUR","date":"2020-10-08"}',
                'USD',
                1.1765,
                true,
            ],
            [
                '{"rates":{"CAD":1.5511},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                1.5511,
                true,
            ],
            [
                '{"rates":{"CAD":1.5599},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                1.5599,
                true,
            ],
            [
                '{"rates":{"CAD":1.5503},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                1.5503,
                true,
            ],
            [
                '{"rates":{"CAD":11.503},"base":"EUR","date":"2020-10-08"}',
                'CAD',
                11.503,
                true,
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

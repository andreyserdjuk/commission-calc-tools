<?php

declare(strict_types=1);

namespace CommissionCalc\Tests\Unit;

use CommissionCalc\ExchangeRatesRestClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ExchangeRatesRestClientTest extends TestCase
{
    /**
     * @covers \CommissionCalc\ExchangeRatesRestClient::__construct()
     * @covers \CommissionCalc\ExchangeRatesRestClient::getRatesData()
     */
    public function testGetRatesData()
    {
        $uri = 'https://examplehost.local/';
        $currencyCode = 'EUR';
        $expectedRequestMethod = 'GET';
        $expectedRequestUri = 'https://examplehost.local/latest?base=' . $currencyCode;
        $expectedRatesData = 'rates data';

        /** @var MockObject|ClientInterface $client */
        $client = $this->getMockBuilder(ClientInterface::class)
            ->onlyMethods(['sendRequest'])
            ->getMock()
        ;

        $bodyStream = $this->createConfiguredMock(
            StreamInterface::class,
            [
                'getContents' => $expectedRatesData,
            ]
        );

        $response = $this->createConfiguredMock(
            ResponseInterface::class,
            [
                'getBody' => $bodyStream,
            ]
        );

        $client->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->callback(
                    fn(RequestInterface $request) =>
                        $expectedRequestMethod === $request->getMethod() &&
                        $expectedRequestUri === (string)$request->getUri()
                )
            )
            ->willReturn($response)
        ;

        $ratesClient = new ExchangeRatesRestClient($client, $uri);
        $ratesData = $ratesClient->getRatesData($currencyCode);

        $this->assertEquals($expectedRatesData, $ratesData->getContents());
    }
}

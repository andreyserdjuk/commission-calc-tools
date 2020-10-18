<?php

declare(strict_types=1);

namespace CommissionCalc\Tests\Unit;

use CommissionCalc\BinlistClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class BinlistClientTest extends TestCase
{
    /**
     * @covers \CommissionCalc\BinlistClient::__construct()
     * @covers \CommissionCalc\BinlistClient::getBinData()
     */
    public function testGetBinData()
    {
        $uri = 'https://examplehost.local/';
        $bin = '12312312';
        $expectedBinData = 'bin data';
        $expectedRequestMethod = 'GET';
        $expectedRequestUri = 'https://examplehost.local/' . $bin;

        /** @var MockObject|ClientInterface $client */
        $client = $this->getMockBuilder(ClientInterface::class)
            ->onlyMethods(['sendRequest'])
            ->getMock()
        ;

        $bodyStream = $this->createConfiguredMock(
            StreamInterface::class,
            [
                'getContents' => $expectedBinData,
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

        $binlistClient = new BinlistClient($client, $uri);
        $binData = $binlistClient->getBinData($bin);

        $this->assertEquals($expectedBinData, $binData->getContents());
    }
}

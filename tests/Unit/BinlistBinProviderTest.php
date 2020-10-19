<?php

declare(strict_types=1);

namespace CommissionCalc\Tests\Unit;

use CommissionCalc\BinlistBinProvider;
use CommissionCalc\BinlistClientInterface;
use CommissionCalc\Exception\ProviderConnectivityException;
use CommissionCalc\Exception\ProviderDataException;
use CommissionCalc\Models\BinCountry;
use CommissionCalc\Models\BinData;
use GuzzleHttp\Exception\InvalidArgumentException;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\MockObject\MockObject;

class BinlistBinProviderTest extends BaseTestCase
{
    /**
     * @dataProvider binDataProvider
     * @covers       \CommissionCalc\BinlistBinProvider::getBinData()
     * @covers       \CommissionCalc\BinlistBinProvider::__construct()
     */
    public function testGetBinData(?string $binDataRespose, bool $isValidServerResponse)
    {
        if (!$isValidServerResponse) {
            $this->expectException(ProviderDataException::class);
        }

        $ratesClientMock = $this->createConfiguredMock(
            BinlistClientInterface::class,
            [
                'getBinData' => Utils::streamFor($binDataRespose),
            ]
        );

        $provider = new BinlistBinProvider($ratesClientMock, $this->getSerializer());
        $binData = $provider->getBinData('123412341234');

        $this->assertInstanceOf(BinData::class, $binData);
        $this->assertInstanceOf(BinCountry::class, $binData->getCountry());
        $this->assertNotEmpty(BinCountry::class, $binData->getCountry()->getAlpha2());
    }

    /**
     * @covers \CommissionCalc\BinlistBinProvider::getBinData()
     * @covers \CommissionCalc\BinlistBinProvider::__construct()
     */
    public function testApiServerError()
    {
        $this->expectException(ProviderConnectivityException::class);

        /** @var MockObject|BinlistClientInterface $clientMock */
        $clientMock = $this->getMockBuilder(BinlistClientInterface::class)
            ->onlyMethods(['getBinData'])
            ->getMock();

        $clientMock
            ->method('getBinData')
            ->willThrowException(new InvalidArgumentException());

        $provider = new BinlistBinProvider($clientMock, $this->getSerializer());
        $provider->getBinData('1234');
    }

    public function binDataProvider()
    {
        return [
            [
                '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Visa/Dankort",'
                . '"prepaid":false,"country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰",'
                . '"currency":"DKK","latitude":56,"longitude":10},"bank":{"name":"Jyske Bank","url":"www.jyskebank.dk",'
                . '"phone":"+4589893300","city":"Hjørring"}}',
                true,
            ],
            [
                '{"country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK",'
                . '"latitude":56,"longitude":10},"bank":{"name":"Jyske Bank","url":"www.jyskebank.dk",'
                . '"phone":"+4589893300","city":"Hjørring"}}',
                false,
            ],
            [
                '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Visa/Dankort",'
                . '"prepaid":false,"country":{"numeric":"208","xxx":"DK","name":"Denmark","emoji":"🇩🇰",'
                . '"currency":"DKK","latitude":56,"longitude":10},"bank":{"name":"Jyske Bank","url":"www.jyskebank.dk",'
                . '"phone":"+4589893300","city":"Hjørring"}}',
                false,
            ],
            [
                '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Visa/Dankort",'
                . '"prepaid":false,"xxx":{"numeric":"208","":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK",'
                . '"latitude":56,"longitude":10},"bank":{"name":"Jyske Bank","url":"www.jyskebank.dk",'
                . '"phone":"+4589893300","city":"Hjørring"}}',
                false,
            ],
            [
                '',
                false,
            ],
        ];
    }
}

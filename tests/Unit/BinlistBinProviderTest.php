<?php

namespace Tests\CommissionCalc\Unit;

use CommissionCalc\BinlistBinProvider;
use CommissionCalc\Models\BinData;
use CommissionCalc\Models\Country;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class BinlistBinProviderTest extends TestCase
{
    /**
     * @dataProvider binDataProvider
     * @covers       \CommissionCalc\BinlistBinProvider::getBinData()
     */
    public function testGetBinData(?string $fileGetContentsResult, bool $isValidServerResponse)
    {
        if (!$isValidServerResponse) {
            $this->expectException(UnexpectedValueException::class);
        }

        /** @var BinlistBinProvider|MockObject $provider */
        $provider = $this->getMockBuilder(BinlistBinProvider::class)
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

        $binData = $provider->getBinData('123412341234');

        $this->assertInstanceOf(BinData::class, $binData);
        $this->assertInstanceOf(Country::class, $binData->getCountry());
        $this->assertNotEmpty(Country::class, $binData->getCountry()->getAlpha2());
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
                true,
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

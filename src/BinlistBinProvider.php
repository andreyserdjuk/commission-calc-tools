<?php

declare(strict_types=1);


namespace CardAmountCalc;


use CardAmountCalc\Models\BinData;
use CardAmountCalc\Models\Country;

class BinlistBinProvider implements BinProviderInterface
{
    public function getBinData(string $bin): BinData
    {
        $data = $this->fetchData($bin);
        $data = json_decode($data, true);

        if (empty($data['country']['alpha2'])) {
            throw new \UnexpectedValueException('Malformed bin data');
        }

        // To map JSON to Objects you can also use a serializer when Models get bigger
        $country = new Country();
        $country->setAlpha2($data['country']['alpha2']);

        $binData = new BinData();
        $binData->setCountry($country);

        return $binData;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function fetchData($bin): string
    {
        return (string) file_get_contents('https://lookup.binlist.net/' . $bin);
    }
}

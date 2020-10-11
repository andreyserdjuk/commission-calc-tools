<?php

declare(strict_types=1);

namespace CommissionCalc;

use CommissionCalc\Models\BinData;
use CommissionCalc\Models\Country;
use UnexpectedValueException;

class BinlistBinProvider implements BinProviderInterface
{
    public function getBinData(string $bin): BinData
    {
        $data = $this->fetchData($bin);
        $data = json_decode($data, true);

        if (empty($data['country']['alpha2'])) {
            throw new UnexpectedValueException('Malformed bin data');
        }

        // To map JSON to Objects you can also use a serializer when Models get bigger
        $country = new Country($data['country']['alpha2']);

        return new BinData($country);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function fetchData($bin): string
    {
        return (string) file_get_contents('https://lookup.binlist.net/' . $bin);
    }
}

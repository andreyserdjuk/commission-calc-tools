<?php

namespace CommissionCalc;

use CommissionCalc\Models\BinData;

interface BinProviderInterface
{
    /**
     * @param string $bin Bank Identification Number (BIN)
     */
    public function getBinData(string $bin): BinData;
}

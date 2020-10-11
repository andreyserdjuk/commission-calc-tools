<?php


namespace CommissionCalc;


use CommissionCalc\Models\BinData;

interface BinProviderInterface
{
    /**
     * @param string $bin Bank Identification Number (BIN)
     * @return mixed
     */
    public function getBinData(string $bin): BinData;
}

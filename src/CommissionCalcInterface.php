<?php

namespace CommissionCalc;

/**
 * Calculates commission in defined currency (provided with DI or bound to class)
 * from given amount and source currency.
 * Logic of calculation can depend on card Issuer.
 */
interface CommissionCalcInterface
{
    /**
     * @param  string $bin            Bank Identification Number (BIN)
     * @param  string $amount         amount calculated in given currency
     * @param  string $sourceCurrency
     * @return string commission in target currency
     */
    public function calcCommission(
        string $bin,
        string $amount,
        string $sourceCurrency
    ): string;
}

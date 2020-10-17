<?php

namespace CommissionCalc;

use CommissionCalc\Models\CurrencyRates;

interface CurrencyRateProviderInterface
{
    public function getRates(string $baseCurrency): CurrencyRates;
}

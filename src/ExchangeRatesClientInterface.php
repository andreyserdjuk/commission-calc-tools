<?php

namespace CommissionCalc;

use Psr\Http\Message\StreamInterface;

interface ExchangeRatesClientInterface
{
    public function getRatesData(string $baseCurrency): StreamInterface;
}

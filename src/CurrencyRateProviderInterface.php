<?php


namespace CommissionCalc;


interface CurrencyRateProviderInterface
{
    public function getRate(string $baseCurrency, string $targetCurrency): float ;
}

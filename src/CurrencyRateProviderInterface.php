<?php


namespace CardAmountCalc;


interface CurrencyRateProviderInterface
{
    public function getRate(string $baseCurrency, string $targetCurrency): float ;
}

<?php

declare(strict_types=1);


namespace CardAmountCalc;

/**
 * Provides currency rate from api.exchangeratesapi.io
 */
class ExchangeratesProvider implements CurrencyRateProviderInterface
{
    public function getRate(string $baseCurrency, string $targetCurrency): float
    {
        $rates = $this->fetchData($baseCurrency);
        $rates = json_decode($rates, true);

        if (empty($rates['rates'][$targetCurrency])) {
            throw new \UnexpectedValueException('Malformed rates data.');
        }

        $rate = (float) $rates['rates'][$targetCurrency];
        $rate = bcmul((string) $rate, '100', 2);
        $rate = ceil($rate);

        return (float) bcdiv((string) $rate, '100', 2);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function fetchData(string $sourceCurrency): string
    {
        return (string) file_get_contents('https://api.exchangeratesapi.io/latest?base='.$sourceCurrency);
    }
}

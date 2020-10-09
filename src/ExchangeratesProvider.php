<?php

declare(strict_types=1);


namespace CardAmountCalc;

use UnexpectedValueException;

/**
 * Provides currency rate from api.exchangeratesapi.io
 */
class ExchangeratesProvider implements CurrencyRateProviderInterface
{
    public function getRate(string $baseCurrency, string $targetCurrency): float
    {
        $rates = json_decode($this->fetchData($baseCurrency), true);

        if (empty($rates['rates'][$targetCurrency])) {
            throw new UnexpectedValueException('Malformed rates data.');
        }

        $rate = (float) $rates['rates'][$targetCurrency];

        return round($rate, 2, PHP_ROUND_HALF_UP);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function fetchData(string $sourceCurrency): string
    {
        return (string) file_get_contents('https://api.exchangeratesapi.io/latest?base='.$sourceCurrency);
    }
}

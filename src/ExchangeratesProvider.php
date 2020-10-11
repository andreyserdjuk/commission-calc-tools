<?php

declare(strict_types=1);


namespace CommissionCalc;

use UnexpectedValueException;

/**
 * Provides currency rate from api.exchangeratesapi.io
 */
class ExchangeratesProvider implements CurrencyRateProviderInterface
{
    public function getRate(string $baseCurrency, string $targetCurrency): float
    {
        if ($baseCurrency === $targetCurrency) {
            return 1.00;
        }

        $rates = json_decode($this->fetchData($baseCurrency), true);

        if (empty($rates['rates'][$targetCurrency])) {
            throw new UnexpectedValueException('Malformed rates data.');
        }

        return (float) $rates['rates'][$targetCurrency];
    }

    /**
     * @codeCoverageIgnore
     */
    protected function fetchData(string $baseCurrency): string
    {
        return (string) file_get_contents('https://api.exchangeratesapi.io/latest?base='.$baseCurrency);
    }
}

<?php

declare(strict_types=1);

namespace CommissionCalc;

use UnexpectedValueException;

/**
 * Calculates commission in EUR from source currency.
 */
class EurCommissionCalc implements CommissionCalcInterface
{
    private BinProviderInterface $binProvider;
    private CurrencyRateProviderInterface $rateProvider;
    private float $europeCommissionRate;
    private float $nonEuropeCommissionRate;

    public function __construct(
        BinProviderInterface $binProvider,
        CurrencyRateProviderInterface $rateProvider,
        float $europeCommissionRate,
        float $nonEuropeCommissionRate
    ) {
        $this->binProvider = $binProvider;
        $this->rateProvider = $rateProvider;
        $this->europeCommissionRate = $europeCommissionRate;
        $this->nonEuropeCommissionRate = $nonEuropeCommissionRate;
    }

    public function calcCommission(string $bin, string $amount, string $sourceCurrency): float
    {
        $rate = $this->rateProvider
            ->getRates($sourceCurrency)
            ->getRate('EUR')
        ;

        if ($rate === null) {
            throw new UnexpectedValueException(sprintf(
                'Rates does not contain rate for "%s" with base currency "%s".',
                $sourceCurrency,
                'EUR'
            ));
        }

        $amountInEur = bcmul((string)$amount, (string)$rate, 2);

        $countryCode = $this->binProvider
            ->getBinData($bin)
            ->getCountry()
            ->getAlpha2()
        ;

        $commissionRate = $this->isEu($countryCode)
            ? $this->europeCommissionRate
            : $this->nonEuropeCommissionRate;

        $commission = (float)bcmul($amountInEur, (string)$commissionRate, 4);

        return $this->ceilFloat($commission, 2);
    }

    private function isEu(string $alpha2): bool
    {
        return in_array(
            $alpha2,
            [
                'AT', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
                'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
            ],
            true
        );
    }

    private function ceilFloat(float $number, int $scale): float
    {
        $preparedToCeil = bcmul((string)$number, '100', 2);
        $ceilNumber = ceil((float)$preparedToCeil);

        return (float)bcdiv((string)$ceilNumber, '100', $scale);
    }
}

<?php

declare(strict_types=1);


namespace CardAmountCalc;


/**
 * Calculates commission in EUR from source currency.
 */
class EurCommissionCalc implements CurrencyCommissionCalcInterface
{
    protected BinProviderInterface $binProvider;
    protected CurrencyRateProviderInterface $rateProvider;
    protected float $europeCommissionRate;
    protected float $nonEuropeCommissionRate;

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

    public function calcCommission(string $bin, float $amount, string $sourceCurrency): float
    {
        $binData = $this->binProvider->getBinData($bin);
        $alpha2 = $binData->getCountry()->getAlpha2();
        $isEu = $this->isEu($alpha2);

        if ($sourceCurrency === 'EUR') {
            $resultAmount = $amount;
        } else {
            $rate = $this->rateProvider->getRate('EUR', $sourceCurrency);
            if ($rate === 0.0) {
                $resultAmount = $amount;
            } else {
                $resultAmount = bcdiv((string) $amount, (string) $rate, 2);
            }
        }

        $commissionRate = $isEu ? $this->europeCommissionRate : $this->nonEuropeCommissionRate;
        $rawCommission = bcmul((string) $resultAmount, (string) $commissionRate, 4);
        $ceilMult100Commission = ceil((float) bcmul($rawCommission, '100', 2));

        return (float) bcdiv((string) $ceilMult100Commission, '100', 2);
    }

    protected function isEu(string $alpha2): bool
    {
        return in_array($alpha2, [
            'AT', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
            'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
        ], true);
    }
}

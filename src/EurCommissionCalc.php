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

        if ('EUR' === $sourceCurrency) {
            $resultAmount = $amount;
        } else {
            $rate = $this->rateProvider->getRate('EUR', $sourceCurrency);
            if (0.0 === $rate) {
                $resultAmount = $amount;
            } else {
                $resultAmount = bcdiv((string) $amount, (string) $rate, 2);
            }
        }

        $commissionRate = $isEu ? $this->europeCommissionRate : $this->nonEuropeCommissionRate;
        $commission = bcmul((string) $resultAmount, (string) $commissionRate, 4);
        $commission = (float) bcmul($commission, '100', 2);
        $commission = ceil($commission);
        $commission = (float) bcdiv((string) $commission, '100', 2);

        return $commission;
    }

    protected function isEu(string $alpha2): bool
    {
        return in_array($alpha2, [
            'AT', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU',
            'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK',
        ], true);
    }
}

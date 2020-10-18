<?php

declare(strict_types=1);

namespace CommissionCalc;

/**
 * Holds logic of parsing payment data from specific input format
 * with next passing it to CommissionCalcInterface.
 */
interface RawCommissionCalcInterface
{
    public function calcCommission(string $paymentData): string;
}

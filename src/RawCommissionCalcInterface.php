<?php

declare(strict_types=1);

namespace CommissionCalc;

use CommissionCalc\Exception\BadPaymentDataException;

/**
 * Holds logic of parsing payment data from specific input format
 * with next passing it to CommissionCalcInterface.
 */
interface RawCommissionCalcInterface
{
    /**
     * @throws BadPaymentDataException
     */
    public function calcCommission(string $paymentData): string;
}

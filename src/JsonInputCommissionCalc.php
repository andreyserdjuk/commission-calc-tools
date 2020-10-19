<?php

declare(strict_types=1);

namespace CommissionCalc;

use CommissionCalc\Exception\BadPaymentDataException;
use CommissionCalc\Models\PaymentData;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Knows how to parse payment data from JSON format
 * and passes it to CommissionCalcInterface.
 */
class JsonInputCommissionCalc implements RawCommissionCalcInterface
{
    private SerializerInterface $serializer;

    private CommissionCalcInterface $commissionCalc;

    public function __construct(SerializerInterface $serializer, CommissionCalcInterface $commissionCalc)
    {
        $this->serializer = $serializer;
        $this->commissionCalc = $commissionCalc;
    }

    /**
     * @inheritDoc
     */
    public function calcCommission(string $paymentData): string
    {
        try {
            /** @var PaymentData $payment */
            $payment = $this->serializer->deserialize($paymentData, PaymentData::class, 'json');
        } catch (ExceptionInterface $e) {
            throw new BadPaymentDataException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $this->commissionCalc->calcCommission(
            $payment->getBin(),
            $payment->getAmount(),
            $payment->getCurrency()
        );
    }
}

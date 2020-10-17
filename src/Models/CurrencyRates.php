<?php

declare(strict_types=1);

namespace CommissionCalc\Models;

use DateTimeInterface;

class CurrencyRates
{
    private array $rates;

    private string $base;

    private DateTimeInterface $date;

    public function getRates(): array
    {
        return $this->rates;
    }

    public function getRate($currencyCode): ?float
    {
        return $currencyCode === $this->base
            ? 1.0
            : $this->rates[$currencyCode] ?? null
        ;
    }

    public function setRates(array $rates): void
    {
        $this->rates = $rates;
    }

    public function getBase(): string
    {
        return $this->base;
    }

    public function setBase(string $base): void
    {
        $this->base = $base;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }
}

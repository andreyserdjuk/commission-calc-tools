<?php

declare(strict_types=1);

namespace CommissionCalc\Models;

class BinData
{
    private BinNumber $number;
    private string $scheme;
    private BinCountry $country;
    private BinBank $bank;
    private ?string $brand;
    private ?string $type;
    private ?bool $prepaid;

    public function __construct(
        BinNumber $number,
        string $scheme,
        BinCountry $country,
        BinBank $bank,
        ?string $brand = null,
        ?string $type = null,
        ?bool $prepaid = null
    ) {
        $this->number = $number;
        $this->scheme = $scheme;
        $this->country = $country;
        $this->bank = $bank;
        $this->brand = $brand;
        $this->type = $type;
        $this->prepaid = $prepaid;
    }

    public function getNumber(): BinNumber
    {
        return $this->number;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getCountry(): BinCountry
    {
        return $this->country;
    }

    public function getBank(): BinBank
    {
        return $this->bank;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getPrepaid(): ?bool
    {
        return $this->prepaid;
    }
}

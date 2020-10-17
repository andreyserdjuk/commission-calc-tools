<?php

declare(strict_types=1);

namespace CommissionCalc\Models;

class BinCountry
{
    private string $numeric;
    private string $alpha2;
    private string $name;
    private string $emoji;
    private string $currency;
    private int $latitude;
    private int $longitude;

    public function __construct(
        string $numeric,
        string $alpha2,
        string $name,
        string $emoji,
        string $currency,
        int $latitude,
        int $longitude
    ) {
        $this->numeric = $numeric;
        $this->alpha2 = $alpha2;
        $this->name = $name;
        $this->emoji = $emoji;
        $this->currency = $currency;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getNumeric(): string
    {
        return $this->numeric;
    }

    public function getAlpha2(): string
    {
        return $this->alpha2;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmoji(): string
    {
        return $this->emoji;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getLatitude(): int
    {
        return $this->latitude;
    }

    public function getLongitude(): int
    {
        return $this->longitude;
    }
}

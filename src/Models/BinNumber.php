<?php

namespace CommissionCalc\Models;

class BinNumber
{
    private int $length;
    private bool $luhn;

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): BinNumber
    {
        $this->length = $length;
        return $this;
    }

    public function isLuhn(): bool
    {
        return $this->luhn;
    }

    public function setLuhn(bool $luhn): BinNumber
    {
        $this->luhn = $luhn;
        return $this;
    }
}

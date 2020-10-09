<?php


namespace CardAmountCalc\Models;


class Country
{
    protected string $alpha2;

    public function getAlpha2(): string
    {
        return $this->alpha2;
    }

    public function setAlpha2(string $alpha2): void
    {
        $this->alpha2 = $alpha2;
    }
}
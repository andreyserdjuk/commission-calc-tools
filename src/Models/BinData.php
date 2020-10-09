<?php


namespace CardAmountCalc\Models;


class BinData
{
    protected Country $country;

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }
}

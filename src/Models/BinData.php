<?php

declare(strict_types=1);


namespace CardAmountCalc\Models;


class BinData
{
    private Country $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }
}

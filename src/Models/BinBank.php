<?php

namespace CommissionCalc\Models;

class BinBank
{
    private string $name;
    private string $url;
    private string $phone;
    private string $city;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): BinBank
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): BinBank
    {
        $this->url = $url;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): BinBank
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): BinBank
    {
        $this->city = $city;
        return $this;
    }
}

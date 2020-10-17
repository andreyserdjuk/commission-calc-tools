<?php

namespace CommissionCalc;

use Psr\Http\Message\StreamInterface;

interface BinlistClientInterface
{
    public function getBinData(string $bin): StreamInterface;
}

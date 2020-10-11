<?php

declare(strict_types = 1);

require __DIR__.'/../vendor/autoload.php';

use CommissionCalc\BinlistBinProvider;
use CommissionCalc\EurCommissionCalc;
use CommissionCalc\ExchangeratesProvider;

$file = new SplFileObject(__DIR__.'/input.txt');

$binProvider = new BinlistBinProvider();
$rateProvider = new ExchangeratesProvider();
$calc = new EurCommissionCalc(
    $binProvider,
    $rateProvider,
    0.01,
    0.02
);

while (
    !$file->eof() &&
    $line = $file->fgets() // skip empty line
) {
    $paymentData = json_decode($line, true);
    $bin = $paymentData['bin'];
    $amount = (float) $paymentData['amount'];
    $currency = $paymentData['currency'];

    $commission = $calc->calcCommission($bin, $amount, $currency);

    echo $commission . PHP_EOL;
}

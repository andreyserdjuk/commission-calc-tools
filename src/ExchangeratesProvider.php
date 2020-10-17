<?php

declare(strict_types=1);

namespace CommissionCalc;

use CommissionCalc\Models\CurrencyRates;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use UnexpectedValueException;

/**
 * Provides currency rates from api.exchangeratesapi.io
 */
class ExchangeratesProvider implements CurrencyRateProviderInterface
{
    private ExchangeRatesClientInterface $client;

    private SerializerInterface $serializer;

    public function __construct(ExchangeRatesClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @throws UnexpectedValueException|ClientExceptionInterface
     */
    public function getRates(string $baseCurrency): CurrencyRates
    {
        $ratesData = $this->client->getRatesData($baseCurrency)->getContents();
        /** @var CurrencyRates $rates */
        $rates = $this->serializer->deserialize($ratesData, CurrencyRates::class, 'json');
//        $rates->getRates()

        return $rates;
    }
}

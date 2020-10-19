<?php

declare(strict_types=1);

namespace CommissionCalc;

use CommissionCalc\Exception\ProviderConnectivityException;
use CommissionCalc\Exception\ProviderDataException;
use CommissionCalc\Exception\ProviderIntegrationException;
use CommissionCalc\Models\CurrencyRates;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

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
     * @throws ProviderIntegrationException
     */
    public function getRates(string $baseCurrency): CurrencyRates
    {
        try {
            $ratesData = $this->client->getRatesData($baseCurrency)->getContents();
            /** @var CurrencyRates $rates */
            $rates = $this->serializer->deserialize($ratesData, CurrencyRates::class, 'json');
        } catch (ClientExceptionInterface $e) {
            throw new ProviderConnectivityException(
                sprintf(
                    'Cannot connect to bin provider "%s", error message: "%s"',
                    __CLASS__,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        } catch (ExceptionInterface $e) {
            throw new ProviderDataException(
                sprintf(
                    'Cannot parse data from bin provider "%s", error message: "%s"',
                    __CLASS__,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }

        return $rates;
    }
}

<?php

namespace CommissionCalc;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;

class ExchangeRatesRestClient implements ExchangeRatesClientInterface
{
    private ClientInterface $client;

    private string $uri;

    public function __construct(ClientInterface $client, string $uri)
    {
        $this->client = $client;
        $this->uri = rtrim($uri, '/');
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function getRatesData(string $baseCurrency): StreamInterface
    {
        $request = new Request('GET', $this->uri . '/latest?base=' . $baseCurrency);

        return $this->client
            ->sendRequest($request)
            ->getBody()
        ;
    }
}

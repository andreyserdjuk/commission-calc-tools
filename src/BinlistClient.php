<?php

namespace CommissionCalc;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;

class BinlistClient implements BinlistClientInterface
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
    public function getBinData(string $bin): StreamInterface
    {
        $request = new Request('GET', $this->uri . '/' . $bin);

        return $this->client
            ->sendRequest($request)
            ->getBody()
        ;
    }
}

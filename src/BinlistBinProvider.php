<?php

declare(strict_types=1);

namespace CommissionCalc;

use CommissionCalc\Models\BinData;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class BinlistBinProvider implements BinProviderInterface
{
    private BinlistClientInterface $client;
    private SerializerInterface $serializer;

    public function __construct(BinlistClientInterface $client, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->serializer = $serializer;
    }

    /**
     * @throws ExceptionInterface|ClientExceptionInterface
     */
    public function getBinData(string $bin): BinData
    {
        $data = $this->client->getBinData($bin)->getContents();
        /** @var BinData $binData */
        $binData = $this->serializer->deserialize($data, BinData::class, 'json');

        return $binData;
    }
}

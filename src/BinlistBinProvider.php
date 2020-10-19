<?php

declare(strict_types=1);

namespace CommissionCalc;

use CommissionCalc\Exception\ProviderConnectivityException;
use CommissionCalc\Exception\ProviderDataException;
use CommissionCalc\Exception\ProviderIntegrationException;
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
     * @throws ProviderIntegrationException
     */
    public function getBinData(string $bin): BinData
    {
        try {
            $data = $this->client->getBinData($bin)->getContents();
            /** @var BinData $binData */
            $binData = $this->serializer->deserialize($data, BinData::class, 'json');
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

        return $binData;
    }
}

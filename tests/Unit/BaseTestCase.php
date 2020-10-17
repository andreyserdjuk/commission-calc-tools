<?php

namespace CommissionCalc\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BaseTestCase extends TestCase
{
    protected function getSerializer()
    {
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $encoders = [new JsonEncoder()];
        $normalizers = [$normalizer, new DateTimeNormalizer()];

        return new Serializer($normalizers, $encoders);
    }
}

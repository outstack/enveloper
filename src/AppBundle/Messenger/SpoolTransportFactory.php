<?php

namespace AppBundle\Messenger;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class SpoolTransportFactory implements TransportFactoryInterface
{
    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        return new SpoolTransport();
    }

    public function supports(string $dsn, array $options): bool
    {
        return 0 === strpos($dsn, 'spool://memory');
    }
}

<?php

namespace Outstack\Enveloper\Infrastructure\Delivery\DeliveryQueue\SymfonyMessenger;

use Outstack\Enveloper\Domain\Delivery\DeliveryQueue;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyMessengerDeliveryQueue implements DeliveryQueue
{
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
    }

    public function append(EmailRequest $emailRequest)
    {
        $this->bus->dispatch($emailRequest);
    }
}
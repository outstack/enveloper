<?php

namespace Outstack\Enveloper\Infrastructure\Delivery\DeliveryQueue\SymfonyMessenger;

use Outstack\Enveloper\Application\AttemptDelivery;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SymfonyMessengerDeliveryQueueHandler implements MessageHandlerInterface
{
    /**
     * @var AttemptDelivery
     */
    private $attemptDelivery;

    public function __construct(AttemptDelivery $attemptDelivery)
    {
        $this->attemptDelivery = $attemptDelivery;
    }

    public function __invoke(EmailRequest $emailRequest)
    {
        \call_user_func($this->attemptDelivery, $emailRequest);
    }
}
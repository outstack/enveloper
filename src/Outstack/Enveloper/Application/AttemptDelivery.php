<?php

namespace Outstack\Enveloper\Application;

use Outstack\Enveloper\Domain\Delivery\AttemptedDelivery;
use Outstack\Enveloper\Domain\Delivery\DeliveryMethod;
use Outstack\Enveloper\Domain\History\EmailDeliveryLog;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Outstack\Enveloper\Domain\Resolution\MessageResolver;
use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLoader;

class AttemptDelivery
{
    /**
     * @var MessageResolver
     */
    private $messageResolver;
    /**
     * @var TemplateLoader
     */
    private $templateLoader;
    /**
     * @var DeliveryMethod
     */
    private $deliveryMethod;
    /**
     * @var EmailDeliveryLog
     */
    private $emailRequestLog;

    public function __construct(
        MessageResolver $messageResolver,
        TemplateLoader $templateLoader,
        DeliveryMethod $deliveryMethod,
        EmailDeliveryLog $emailRequestLog
    )
    {
        $this->messageResolver = $messageResolver;
        $this->templateLoader = $templateLoader;
        $this->deliveryMethod = $deliveryMethod;
        $this->emailRequestLog = $emailRequestLog;
    }
    public function __invoke(EmailRequest $emailRequest)
    {
        $resolvedMessage = $this->messageResolver->resolve(
            $this->templateLoader->find($emailRequest->getTemplate()),
            $emailRequest->getParameters()
        );

        $attemptedDelivery = new AttemptedDelivery(
            $emailRequest,
            $this->emailRequestLog->countDeliveryAttempts($emailRequest),
            $resolvedMessage,
            new \DateTimeImmutable('now')
        );

        $this->deliveryMethod->attemptDelivery($emailRequest, $resolvedMessage);
        $this->emailRequestLog->recordAttemptedDelivery($attemptedDelivery);

    }
}
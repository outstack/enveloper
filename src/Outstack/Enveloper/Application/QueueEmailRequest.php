<?php

namespace Outstack\Enveloper\Application;

use Outstack\Enveloper\Domain\Delivery\DeliveryQueue;
use Outstack\Enveloper\Domain\History\EmailDeliveryLog;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Outstack\Enveloper\Domain\Resolution\MessageResolver;
use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLoader;

class QueueEmailRequest
{
    /**
     * @var EmailDeliveryLog
     */
    private $emailRequestLog;
    /**
     * @var DeliveryQueue
     */
    private $deliveryQueue;
    /**
     * @var MessageResolver
     */
    private $messageResolver;
    /**
     * @var TemplateLoader
     */
    private $templateLoader;

    public function __construct(EmailDeliveryLog $emailRequestLog, DeliveryQueue $deliveryQueue, MessageResolver $messageResolver, TemplateLoader $templateLoader)
    {
        $this->emailRequestLog = $emailRequestLog;
        $this->deliveryQueue = $deliveryQueue;
        $this->messageResolver = $messageResolver;
        $this->templateLoader = $templateLoader;
    }

    public function __invoke(EmailRequest $emailRequest)
    {
        $template = $this->templateLoader->find($emailRequest->getTemplate());

        $this->messageResolver->validate($template, $emailRequest->getParameters());
        $this->emailRequestLog->recordInitialRequest($emailRequest);
        $this->deliveryQueue->append(
            $emailRequest,
            $this->messageResolver->resolve(
                $template,
                $emailRequest->getParameters()
            )
        );
    }
}
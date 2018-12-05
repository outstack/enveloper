<?php

namespace AppBundle\Controller;

use Outstack\Components\ApiProvider\ApiProblemDetails\ApiProblemFactory;
use Outstack\Enveloper\Domain\Delivery\AttemptedDelivery;
use Outstack\Enveloper\Domain\Email\Email;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Outstack\Enveloper\Domain\Email\Participants\Participant;
use Outstack\Enveloper\Domain\History\EmailDeliveryLog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class BaseController extends AbstractController
{
    /**
     * @var EmailDeliveryLog
     */
    protected $emailDeliveryLog;
    /**
     * @var ApiProblemFactory
     */
    protected $problemFactory;
    /**
     * @var string
     */
    protected $projectDir;

    public function __construct(EmailDeliveryLog $emailDeliveryLog, ApiProblemFactory $problemFactory, string $projectDir)
    {
        $this->emailDeliveryLog = $emailDeliveryLog;
        $this->problemFactory = $problemFactory;
        $this->projectDir = $projectDir;
    }

    protected function serialiseAttemptedDelivery(AttemptedDelivery $attemptedDelivery): array
    {
        $resolved = $attemptedDelivery->getResolvedMessage();
        $messageData = [
            '@id' => $this->generateUrl(
                'app.delivery_attempts.byIndex',
                [
                    'id' => $attemptedDelivery->getEmailRequest()->getId(),
                    'index' => $attemptedDelivery->getAttemptNumber()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'attemptedAt' => $attemptedDelivery->getAttemptDate()->format(\DateTime::ATOM),
            'resolved' => [
                'subject' => $resolved->getSubject(),
                'sender' => $this->serialiseParticipant($resolved->getSender()),
                'content' => [
                    '@id' => $this->generateUrl(
                        'app.delivery_attempts.view.content',
                        [
                            'id' => $attemptedDelivery->getEmailRequest()->getId(),
                            'index' => $attemptedDelivery->getAttemptNumber()
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'availableContentTypes' => $this->serialiseAvailableContentTypes(
                        $attemptedDelivery->getResolvedMessage()
                    )
                ],
                'recipients' => [
                    'to' => array_map([$this, 'serialiseParticipant'],
                        $resolved->getTo()->getIterator()->getArrayCopy()),
                    'cc' => array_map([$this, 'serialiseParticipant'],
                        $resolved->getCc()->getIterator()->getArrayCopy()),
                    'bcc' => array_map([$this, 'serialiseParticipant'],
                        $resolved->getBcc()->getIterator()->getArrayCopy()),
                ]
            ]
        ];
        return $messageData;
    }

    protected function serialiseParticipant(Participant $participant)
    {
        return [
            'name'  => $participant->isNamed()
                ? $participant->getName()
                : null,
            'email' => (string) $participant->getEmailAddress()
        ];
    }


    protected function serialiseSentMessage(EmailRequest $sentMessage): array
    {
        $messageData = [
            '@id' => $this->generateUrl(
                'app.outbox.view',
                [
                    'id' => $sentMessage->getId()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'template' => $sentMessage->getTemplate(),
            'parameters' => $sentMessage->getParameters(),
            'requestedAt' => $sentMessage->getRequestedAt()->format(\DateTime::ATOM),
            'deliveryAttempts' => $this->generateUrl('app.delivery_attempts.list', ['id' => $sentMessage->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
        return $messageData;
    }

    protected function serialiseAvailableContentTypes(Email $message)
    {
        $availableContentTypes = [
            'application/json',
            'text/html'
        ];
        if ($message->getText()) {
            $availableContentTypes[] = 'text/plain';
        }

        return $availableContentTypes;
    }

    protected function serialiseMessageContentsNegotiatingType(Request $request, Email $message)
    {
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        if (empty($acceptableContentTypes)) {
            $acceptableContentTypes[] = 'application/json';
        }

        foreach ($acceptableContentTypes as $contentType) {
            if ($contentType === 'text/plain' && $message->getText()) {

                return new Response($message->getText(), 200, ['Content-type' => 'text/plain']);
            }
            if ($contentType === 'text/html') {
                return new Response($message->getHtml(), 200, ['Content-type' => 'text/html']);
            }

            if ($contentType === 'application/json') {
                return new JsonResponse(['html' => $message->getHtml(), 'text' => $message->getText()], 200, ['Content-type' => 'application/json']);
            }
        }

        $availableContentTypes = $this->serialiseAvailableContentTypes($message);

        return $this->problemFactory
            ->createProblem(406, 'Not Acceptable')
            ->setDetail('No version of this email matching your Accept header could be found')
            ->addField('availableContentTypes', $availableContentTypes)
            ->buildJsonResponse();

    }
}
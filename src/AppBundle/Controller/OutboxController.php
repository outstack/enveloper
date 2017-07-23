<?php

namespace AppBundle\Controller;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Outstack\Enveloper\Mail\Message;
use Outstack\Enveloper\Mail\Participants\ParticipantList;
use Outstack\Enveloper\Resolution\MessageResolver;
use Outstack\Enveloper\Templates\Loader\TemplateLoader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class OutboxController extends Controller
{
    /**
     * @Route("/outbox", name="homepage")
     */
    public function indexAction(Request $request, MessageResolver $resolver, TemplateLoader $templateLoader, SwiftMailerInterface $mailer)
    {
        $payload = json_decode($request->getContent(), true);

        $message = $resolver->resolve(
            $templateLoader->find($payload['template']),
            $payload['parameters']
        );

        $mailer->send(
            $this->convertToSwiftMessage($message)
        );

        return new Response('', 204);
    }

    private function convertToSwiftMessage(Message $message)
    {
        $swiftTo    = $this->convertToSwiftRecipientArray($message->getTo());
        $swiftCc    = $this->convertToSwiftRecipientArray($message->getCc());
        $swiftBcc   = $this->convertToSwiftRecipientArray($message->getBcc());
        $swiftFrom  = $this->convertToSwiftRecipientArray(new ParticipantList([$message->getSender()]));

        $swiftMessage = (new \Swift_Message())
            ->setSubject($message->getSubject())
            ->setFrom($swiftFrom)
            ->setTo($swiftTo)
            ->setCc($swiftCc)
            ->setBcc($swiftBcc);

        foreach ($message->getAttachments() as $attachment) {
            $swiftMessage->attach(
                new \Swift_Attachment($attachment->getData(), $attachment->getFilename())
            );
        }
        $swiftMessage
            ->setBody($message->getHtml(), 'text/html')
            ->addPart($message->getText(), 'text/plain')
        ;

        return $swiftMessage;

    }

    private function convertToSwiftRecipientArray(ParticipantList $recipientList)
    {
        $swiftArray = [];
        foreach ($recipientList->getIterator() as $recipient) {

            if ($recipient->isNamed()) {
                $swiftArray[(string) $recipient->getEmailAddress()] = $recipient->getName();
            } else {
                $swiftArray[] = (string) $recipient->getEmailAddress();
            }
        }

        return $swiftArray;

    }
}

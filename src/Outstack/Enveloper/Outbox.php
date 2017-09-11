<?php

namespace Outstack\Enveloper;

use Outstack\Enveloper\Folders\SentMessagesFolder;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Mail\SentMessage;
use Outstack\Enveloper\Resolution\MessageResolver;
use Outstack\Enveloper\Templates\Loader\TemplateLoader;
use Outstack\Enveloper\Templates\Template;
use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Outstack\Enveloper\Mail\Message;
use Outstack\Enveloper\Mail\Participants\ParticipantList;


class Outbox
{
    /**
     * @var SentMessagesFolder
     */
    private $sentMessages;
    /**
     * @var MessageResolver
     */
    private $messageResolver;
    /**
     * @var TemplateLoader
     */
    private $templateLoader;
    /**
     * @var SwiftMailerInterface
     */
    private $mailer;

    public function __construct(MessageResolver $messageResolver, TemplateLoader $templateLoader, SwiftMailerInterface $mailer, SentMessagesFolder $sentMessages)
    {
        $this->messageResolver = $messageResolver;
        $this->templateLoader = $templateLoader;
        $this->mailer = $mailer;
        $this->sentMessages = $sentMessages;
    }

    public function send(string $templateName, array $parameters)
    {
        $message = $this->messageResolver->resolve(
            $this->templateLoader->find($templateName),
            $parameters
        );

        $this->mailer->send(
            $this->convertToSwiftMessage($message)
        );

        $this->sentMessages->record(
            new SentMessage($templateName, $parameters, $message)
        );
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
                continue;
            }
            $swiftArray[] = (string) $recipient->getEmailAddress();

        }

        return $swiftArray;

    }
}
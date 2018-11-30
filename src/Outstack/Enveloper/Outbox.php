<?php

namespace Outstack\Enveloper;

use Outstack\Enveloper\Folders\SentMessagesFolder;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Mail\OutboxItem;
use Outstack\Enveloper\Resolution\MessageResolver;
use Outstack\Enveloper\Templates\Loader\TemplateLoader;
use Outstack\Enveloper\Templates\Template;
use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Outstack\Enveloper\Mail\Message;
use Outstack\Enveloper\Mail\Participants\ParticipantList;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;


class Outbox implements MessageHandlerInterface
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
    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus, MessageResolver $messageResolver, TemplateLoader $templateLoader, SwiftMailerInterface $mailer, SentMessagesFolder $sentMessages)
    {
        $this->messageResolver = $messageResolver;
        $this->templateLoader = $templateLoader;
        $this->mailer = $mailer;
        $this->sentMessages = $sentMessages;
        $this->messageBus = $messageBus;
    }

    public function queue(string $templateName, object $parameters)
    {
        $this->messageBus->dispatch(
            new OutboxItem(
                $templateName,
                $parameters,
                null
            )
        );
    }

    public function __invoke(OutboxItem $message)
    {
        $this->send($message);
    }

    public function send(OutboxItem $message)
    {
        $message->setResolvedMessage(
            $this->messageResolver->resolve(
                $this->templateLoader->find($message->getTemplate()),
                $message->getParameters()
            )
        );

        $this->mailer->send(
            $this->convertToSwiftMessage($message->getResolvedMessage())
        );

        $this->sentMessages->record($message);
    }

    public function preview(string $templateName, object $parameters): Message
    {
        return $this->messageResolver->resolve(
            $this->templateLoader->find($templateName),
            $parameters
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
        $body = $swiftMessage->setBody($message->getHtml(), 'text/html');
        if ($message->getText()) {
            $body->addPart($message->getText(), 'text/plain');
        }

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
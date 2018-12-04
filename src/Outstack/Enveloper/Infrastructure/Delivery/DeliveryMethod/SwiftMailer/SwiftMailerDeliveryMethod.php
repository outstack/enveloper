<?php

namespace Outstack\Enveloper\Infrastructure\Delivery\DeliveryMethod\SwiftMailer;

use Outstack\Enveloper\Domain\Delivery\DeliveryMethod;
use Outstack\Enveloper\Domain\Email\Email;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Outstack\Enveloper\Domain\Email\Participants\ParticipantList;
use Outstack\Enveloper\Domain\Delivery\AttemptedDelivery;

class SwiftMailerDeliveryMethod implements DeliveryMethod
{
    /**
     * @var SwiftMailerInterface
     */
    private $mailer;

    public function __construct(SwiftMailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function attemptDelivery(EmailRequest $emailRequest, Email $email)
    {
        $this->mailer->send(
            $this->convertToSwiftMessage($email)
        );
    }

    private function convertToSwiftMessage(Email $message)
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
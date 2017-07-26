<?php

namespace Outstack\Enveloper\SwiftMailerBridge;

class SwiftMailerRecordingDecorator implements SwiftMailerInterface
{
    /**
     * @var SwiftMailerInterface
     */
    private $mailer;

    private $sentMessages = [];
    /**
     * @var bool
     */
    private $deliverMessages;

    public function __construct(SwiftMailerInterface $mailer, bool $deliverMessages)
    {
        $this->mailer = $mailer;
        $this->deliverMessages = $deliverMessages;
    }

    public function getMessageCount()
    {
        return count($this->sentMessages);
    }

    public function getMessages(): array
    {
        return $this->sentMessages;
    }

    public function send(\Swift_Message $message)
    {
        if ($this->deliverMessages) {
            $this->mailer->send($message);
        }
        $this->sentMessages[] = $message;
    }
}
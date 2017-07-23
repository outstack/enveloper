<?php

namespace Outstack\Enveloper\SwiftMailerBridge;

class SwiftMailerImplementation implements SwiftMailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(\Swift_Message $message)
    {
        return $this->mailer->send($message);
    }
}
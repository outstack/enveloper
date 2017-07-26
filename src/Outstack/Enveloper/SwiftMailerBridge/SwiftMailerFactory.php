<?php

namespace Outstack\Enveloper\SwiftMailerBridge;

use Swift_Mailer;
use Swift_SmtpTransport;

class SwiftMailerFactory
{
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function create(): SwiftMailerInterface
    {
        $transport = new Swift_SmtpTransport(
            $this->options['host'],
            $this->options['port']
        );
        $transport
            ->setUsername($this->options['username'])
            ->setPassword($this->options['password'])
        ;
        if ($this->options['encryption']) {
            $transport->setEncryption($this->options['encryption']);
        }

        $mailer = new SwiftMailerImplementation(new Swift_Mailer($transport));
        if ($this->options['record']) {
            $mailer = new SwiftMailerRecordingDecorator($mailer, $this->options['deliver_messages']);
        }

        return $mailer;
    }
}
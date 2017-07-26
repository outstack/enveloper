<?php

namespace Outstack\Enveloper\SwiftMailerBridge;

interface SwiftMailerInterface
{
    public function send(\Swift_Message $message);
}
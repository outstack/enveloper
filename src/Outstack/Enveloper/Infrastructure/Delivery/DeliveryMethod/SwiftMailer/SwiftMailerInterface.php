<?php

namespace Outstack\Enveloper\Infrastructure\Delivery\DeliveryMethod\SwiftMailer;

interface SwiftMailerInterface
{
    public function send(\Swift_Message $message);
}
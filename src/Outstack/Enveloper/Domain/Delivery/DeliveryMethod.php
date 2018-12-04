<?php

namespace Outstack\Enveloper\Domain\Delivery;

use Outstack\Enveloper\Domain\Email\Email;
use Outstack\Enveloper\Domain\Email\EmailRequest;

interface DeliveryMethod
{
    public function attemptDelivery(EmailRequest $emailRequest, Email $email);
}
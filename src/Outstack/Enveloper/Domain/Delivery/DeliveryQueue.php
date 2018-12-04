<?php

namespace Outstack\Enveloper\Domain\Delivery;

use Outstack\Enveloper\Domain\Email\EmailRequest;

interface DeliveryQueue
{
    public function append(EmailRequest $emailRequest);
}
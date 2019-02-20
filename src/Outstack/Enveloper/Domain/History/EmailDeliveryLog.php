<?php

namespace Outstack\Enveloper\Domain\History;

use Outstack\Enveloper\Domain\Delivery\AttemptedDelivery;
use Outstack\Enveloper\Domain\Email\EmailRequest;

interface EmailDeliveryLog
{
    public function recordInitialRequest(EmailRequest $emailRequest);
    public function recordAttemptedDelivery(AttemptedDelivery $attemptedDelivery);

    /**
     * @return \Generator|EmailRequest[]
     */
    public function listAll();

    public function deleteAll(): void;

    public function find(string $id): EmailRequest;

    public function findDeliveryAttempts(EmailRequest $emailRequest);

    public function countDeliveryAttempts($emailRequest): int;

    public function findDeliveryAttempt(string $id, int $index): AttemptedDelivery;

}
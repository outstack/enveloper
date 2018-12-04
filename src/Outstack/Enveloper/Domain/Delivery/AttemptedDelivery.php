<?php

namespace Outstack\Enveloper\Domain\Delivery;

use Outstack\Enveloper\Domain\Email\Email;
use Outstack\Enveloper\Domain\Email\EmailRequest;

class AttemptedDelivery
{
    /**
     * @var EmailRequest
     */
    private $emailRequest;
    /**
     * @var Email
     */
    private $resolvedMessage;
    /**
     * @var \DateTimeImmutable
     */
    private $attemptDate;

    private $id;
    /**
     * @var int
     */
    private $attemptNumber;

    public function __construct(EmailRequest $emailRequest, int $attemptNumber, Email $resolvedMessage, \DateTimeImmutable $attemptDate)
    {
        $this->emailRequest = $emailRequest;
        $this->resolvedMessage = $resolvedMessage;
        $this->attemptDate = $attemptDate;
        $this->attemptNumber = $attemptNumber;
    }

    public function getAttemptNumber(): int
    {
        return $this->attemptNumber;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmailRequest(): EmailRequest
    {
        return $this->emailRequest;
    }

    public function getResolvedMessage(): Email
    {
        return $this->resolvedMessage;
    }


    public function getAttemptDate(): \DateTimeImmutable
    {
        return $this->attemptDate;
    }
}
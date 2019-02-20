<?php

namespace Outstack\Enveloper\Domain\Email\Participants;

class Participant
{
    private $name;
    private $emailAddress;

    public function __construct(?string $name, EmailAddress $emailAddress)
    {
        if ('' === $name) {
            throw new \LogicException(
                "Name passed to constructor of recipient ($emailAddress) should be `null` or a non-empty string."
            );
        }

        $this->name = $name;
        $this->emailAddress = $emailAddress;
    }

    public function isNamed()
    {
        return $this->name !== null;
    }

    public function getName(): ?string
    {
        if ($this->name === null) {
            return null;
        }
        return $this->name;
    }

    public function getEmailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }

    public function __toString()
    {
        return "{$this->name} <{$this->emailAddress}>";
    }
}
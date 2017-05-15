<?php

namespace Outstack\Enveloper\Mail\Participants;

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
        return !is_null($this->name);
    }

    public function getName(): string
    {
        if (is_null($this->name)) {
            throw new \LogicException("Call isNamed first to check if the recipient has a name");
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
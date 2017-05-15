<?php

namespace Outstack\Enveloper\Templates;

class ParticipantTemplate
{
    /**
     * @var string
     */
    private $emailAddress;
    /**
     * @var null|string
     */
    private $name;
    /**
     * @var null
     */
    private $iterateOver;

    public function __construct(?string $name, string $emailAddress, ?string $iterateOver = null)
    {
        $this->name = $name;
        $this->emailAddress = $emailAddress;
        $this->iterateOver = $iterateOver;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIterateOver()
    {
        return $this->iterateOver;
    }
}
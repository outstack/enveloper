<?php

namespace Outstack\Enveloper\Domain\Email\Participants;

class EmailAddressNotValid extends \Exception
{
    private $address;

    public function __construct(string $address)
    {
        $this->address = $address;
        parent::__construct("The email address $address failed validation");
    }

    public function getAddress(): string
    {
        return $this->address;
    }
}
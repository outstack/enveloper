<?php

namespace Outstack\Enveloper\Domain\Email\Participants;

class EmailAddress
{
    /**
     * @var string
     */
    private $address;

    public function __construct(string $address)
    {
        $validatedAddress = filter_var($address, FILTER_VALIDATE_EMAIL);
        if (false === $validatedAddress) {
            throw new EmailAddressNotValid($address);
        }

        $this->address = $validatedAddress;
    }

    public function __toString()
    {
        return $this->address;
    }
}
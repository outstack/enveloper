<?php

namespace Outstack\Enveloper\Domain\History\Exceptions;

class EmailRequestNotFound extends \Exception
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
        parent::__construct("Email request with id `$id` not found");
    }

    public function getId(): string
    {
        return $this->id;
    }
}
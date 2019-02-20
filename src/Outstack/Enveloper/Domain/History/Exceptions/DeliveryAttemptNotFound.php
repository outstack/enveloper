<?php

namespace Outstack\Enveloper\Domain\History\Exceptions;

class DeliveryAttemptNotFound extends \Exception
{
    private $id;
    /**
     * @var int
     */
    private $index;

    public function __construct(string $id, int $index)
    {
        $this->id = $id;
        $this->index = $index;
        parent::__construct("Delivery attempt $index for email request with id `$id` not found");
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
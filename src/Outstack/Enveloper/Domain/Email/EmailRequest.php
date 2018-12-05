<?php

namespace Outstack\Enveloper\Domain\Email;

class EmailRequest
{
    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $parameters;

    private $id;
    /**
     * @var \DateTimeImmutable
     */
    private $requestedAt;

    public function __construct(string $template, object $parameters, \DateTimeImmutable $requestedAt)
    {
        $this->template = $template;
        $this->parameters = $parameters;
        $this->requestedAt = $requestedAt;
    }

    public function getRequestedAt(): \DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getParameters(): object
    {
        return (object) $this->parameters;
    }
}
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

    public function __construct(string $template, object $parameters)
    {
        $this->template = $template;
        $this->parameters = $parameters;
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